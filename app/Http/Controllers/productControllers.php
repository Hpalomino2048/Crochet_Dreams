<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductColor;
use App\Services\ProductService;
use App\Services\SkuGenerator;
use App\Http\Traits\HandlesProductFiles;
use App\Http\Traits\HandlesProductsColors;
use App\Http\Traits\FormatsProductsData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductControllers extends Controller
{
    use HandlesProductFiles, HandlesProductsColors, FormatsProductsData;

    public function __construct(
        protected ProductService $productService,
        protected SkuGenerator $skuGenerator
    ) {}

    /**
     * Muestra el formulario de creación de productos.
     */
    public function create(): Response
    {
        return Inertia::render('admin/Create_Product', [
            'formDefaults' => [
                'sku'          => $this->skuGenerator->generate(),
                'name'         => '',
                'slug'         => '',
                'descriptions' => '',
                'price'        => null,
                'currency'     => 'MXN',
                'stock'        => 0,
                'weight'       => null,
                'thumbnail'    => null,
                'image'        => [],
                'category'     => null,
                'subcategory'  => null,
                'tamaño'       => null,
            ],
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $this->productService->validate($request);
        
        DB::beginTransaction();
        
        try {
            $product = $this->productService->create($validated, $request);
            
            if ($request->has('colors') && is_array($request->colors)) {
                $this->createProductColors($product, $request->colors, $request);
            }
            
            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Producto creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->cleanupUploadedFiles($request, $validated);
            
            Log::error('Error al crear producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la lista de productos.
     */
    public function index(): Response
    {
        $products = Product::with('colors')
            ->latest()
            ->paginate(15)
            ->through(fn($product) => $this->formatProductForList($product));

        return Inertia::render('admin/Products', [
            'products' => $products,
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id): Response
    {
        $product = Product::with('colors')->findOrFail($id);

        return Inertia::render('admin/Edit_Product', [
            'product' => $this->formatProductForEdit($product),
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    /**
     * Muestra la página con la lista de productos para ACTUALIZAR
     * usando el componente Vue con modal de edición.
     */
   public function updateList(): Response
    {
        $products = Product::with('colors')
            ->latest()
            ->get()
            ->map(function ($product) {
                return [
                    'id'           => $product->id,
                    'name'         => $product->name,
                    'sku'          => $product->sku,
                    'descriptions' => $product->descriptions ?? '',
                    'price'        => $product->price,
                    'currency'     => $product->currency,
                    'stock'        => $product->stock,
                    'thumbnail'    => $product->thumbnail
                        ? asset('storage/' . $product->thumbnail)
                        : null,
                    'colors'       => $product->colors->map(function ($color) {
                        return [
                            'id'         => $color->id,
                            'name'       => $color->name,
                            'code'       => $color->code,
                            'stock'      => $color->stock,
                            'is_default' => (bool) $color->is_default,
                            'image'      => $color->image
                            ? asset('storage/' . $color->image)
                            : null,
                            'gallery'    => collect($color->gallery ?? [])
                                ->map(fn($img) => asset('storage/' . $img))
                                ->toArray(),
                        ];
                    })->toArray(),
                    'has_variants' => $product->colors->count() > 0,
                    'created_at'   => $product->created_at?->toISOString(),
                ];
            });

        return Inertia::render('admin/Update_Product', [
            'products' => $products,
            'flash'    => session()->only(['success', 'error']),
        ]);
    }


    /**
     * Actualiza un producto existente.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Validación específica para actualización parcial desde el modal
        $validated = $request->validate([
            // Campos básicos del producto
            'descriptions' => ['required', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            
            // Colores
            'colors'                      => ['nullable', 'array'],
            'colors.*.id'                 => ['nullable', 'integer'],
            'colors.*.name'               => ['required', 'string', 'max:100'],
            'colors.*.code'               => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.*.stock'              => ['required', 'integer', 'min:0'],
            'colors.*.is_default'         => ['nullable'],
            'colors.*.image'              => ['nullable', 'image', 'max:5120'],
            'colors.*.gallery'            => ['nullable', 'array'],
            'colors.*.gallery.*'          => ['nullable', 'image', 'max:5120'],
            'colors.*.removed_gallery'    => ['nullable', 'array'],
            'colors.*.removed_gallery.*'  => ['nullable', 'string'],
            
            // Colores a eliminar
            'removed_colors'   => ['nullable', 'array'],
            'removed_colors.*' => ['nullable', 'integer'],
        ]);

        DB::beginTransaction();

        try {
            // 1. Actualizar datos básicos del producto
            $product->update([
                'descriptions' => $validated['descriptions'],
                'price'        => $validated['price'],
                'stock'        => $validated['stock'],
            ]);

            // 2. Eliminar colores marcados para eliminación
            if (!empty($request->removed_colors)) {
                $this->deleteSpecificColors($product, $request->removed_colors);
            }

            // 3. Actualizar/crear colores
            if (!empty($validated['colors'])) {
                $this->processColorsUpdate($product, $validated['colors'], $request);
            }

            DB::commit();

            return redirect()
                ->route('admin.products.update-list')
                ->with('success', 'Producto actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar producto', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'product_id' => $id,
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Elimina colores específicos por sus IDs
     */
    protected function deleteSpecificColors(Product $product, array $colorIds): void
    {
        foreach ($colorIds as $colorId) {
            $color = ProductColor::where('id', $colorId)
                ->where('product_id', $product->id)
                ->first();
            
            if ($color) {
                $this->deleteColorFiles($color);
                $color->delete();
            }
        }
    }

    /**
     * Procesa la actualización de colores (crear nuevos o actualizar existentes)
     */
    protected function processColorsUpdate(Product $product, array $colors, Request $request): void
    {
        foreach ($colors as $index => $colorData) {
            $colorId = $colorData['id'] ?? null;
            
            if ($colorId) {
                // Actualizar color existente
                $color = ProductColor::where('id', $colorId)
                    ->where('product_id', $product->id)
                    ->first();
                
                if ($color) {
                    $this->updateColorWithGalleryManagement($color, $colorData, $request, $index);
                }
            } else {
                // Crear nuevo color
                $this->createNewColorFromModal($product, $colorData, $request, $index);
            }
        }
    }

    /**
     * Actualiza un color existente con manejo de galería individual
     */
    protected function updateColorWithGalleryManagement(
        ProductColor $color, 
        array $colorData, 
        Request $request, 
        int $index
    ): void {
        $data = [
            'name'       => $colorData['name'],
            'code'       => $colorData['code'],
            'stock'      => $colorData['stock'],
            'is_default' => filter_var($colorData['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

        // Manejar imagen principal
        if ($request->hasFile("colors.{$index}.image")) {
            if ($color->image) {
                Storage::disk('public')->delete($color->image);
            }
            $data['image'] = $request->file("colors.{$index}.image")
                ->store('products/colors', 'public');
        }

        // Manejar galería - primero eliminar las marcadas
        $currentGallery = $color->gallery ?? [];
        
        if (!empty($colorData['removed_gallery'])) {
            $currentGallery = $this->removeGalleryImages($currentGallery, $colorData['removed_gallery']);
        }

        // Agregar nuevas imágenes a la galería
        if ($request->hasFile("colors.{$index}.gallery")) {
            foreach ($request->file("colors.{$index}.gallery") as $file) {
                if ($file && $file->isValid()) {
                    $currentGallery[] = $file->store('products/colors/gallery', 'public');
                }
            }
        }

        $data['gallery'] = !empty($currentGallery) ? array_values($currentGallery) : null;

        $color->update($data);
    }

    /**
     * Elimina imágenes específicas de la galería
     */
    protected function removeGalleryImages(array $currentGallery, array $imagesToRemove): array
    {
        foreach ($imagesToRemove as $imageUrl) {
            // Convertir URL completa a path relativo
            // La URL viene como: http://domain.com/storage/products/colors/gallery/image.jpg
            // Necesitamos: products/colors/gallery/image.jpg
            
            $relativePath = $this->extractRelativePath($imageUrl);
            
            $key = array_search($relativePath, $currentGallery);
            if ($key !== false) {
                Storage::disk('public')->delete($relativePath);
                unset($currentGallery[$key]);
            }
        }
        
        return array_values($currentGallery);
    }

    /**
     * Extrae el path relativo de una URL de storage
     */
    protected function extractRelativePath(string $fullUrl): string
    {
        // Buscar '/storage/' en la URL y obtener todo después de eso
        if (preg_match('/\/storage\/(.+)$/', $fullUrl, $matches)) {
            return $matches[1];
        }
        
        // Si no tiene /storage/, intentar con asset path
        $assetUrl = asset('storage/');
        if (str_starts_with($fullUrl, $assetUrl)) {
            return substr($fullUrl, strlen($assetUrl));
        }
        
        // Devolver el path tal cual si no coincide con ningún patrón
        return ltrim($fullUrl, '/');
    }

    /**
     * Crea un nuevo color desde el modal
     */
    protected function createNewColorFromModal(
        Product $product, 
        array $colorData, 
        Request $request, 
        int $index
    ): ProductColor {
        $data = [
            'product_id' => $product->id,
            'name'       => $colorData['name'],
            'code'       => $colorData['code'],
            'stock'      => $colorData['stock'],
            'is_default' => filter_var($colorData['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

        if ($request->hasFile("colors.{$index}.image")) {
            $data['image'] = $request->file("colors.{$index}.image")
                ->store('products/colors', 'public');
        }

        if ($request->hasFile("colors.{$index}.gallery")) {
            $galleryPaths = [];
            foreach ($request->file("colors.{$index}.gallery") as $file) {
                if ($file && $file->isValid()) {
                    $galleryPaths[] = $file->store('products/colors/gallery', 'public');
                }
            }
            $data['gallery'] = !empty($galleryPaths) ? $galleryPaths : null;
        }

        return ProductColor::create($data);
    }


    public function deleteList(): Response
    {
        $products = Product::with('colors')
            ->latest()
            ->paginate(50)
            ->through(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'descriptions' => $product->descriptions,
                'price' => $product->price,
                'currency' => $product->currency,
                'stock' => $product->stock,
                // ✅ Asegúrate de usar asset() aquí
                'thumbnail' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                'colors' => $product->colors->map(fn($color) => [
                    'id' => $color->id,
                    'name' => $color->name,
                    'hex_code' => $color->code, // o hex_code según tu base de datos
                ]),
                'has_variants' => $product->colors->count() > 0,
                'created_at' => $product->created_at,
            ]);

        return Inertia::render('admin/Delete_Product', [
            'products' => $products,
            'flash' => session()->only(['success', 'error']),
        ]);
    }
        /**
     * Muestra la página de confirmación para eliminar un producto específico.
     */
        public function deleteConfirm($id): Response
        {
            $product = Product::with('colors')->findOrFail($id);

            return Inertia::render('admin/Delete_Product', [
                'product' => $this->formatProductForEdit($product),
                'flash' => session()->only(['success', 'error']),
            ]);
        }

        public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $this->deleteProductFiles($product);
            
            foreach ($product->colors as $color) {
                $this->deleteColorFiles($color);
            }
            
            $product->delete();

            // ✅ Cambiar la redirección
            return redirect()
                ->route('admin.Delete_Product') // En lugar de admin.products.index
                ->with('success', 'Producto eliminado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar producto', [
                'error' => $e->getMessage(),
                'product_id' => $id
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el producto.');
        }
    }

    /**
     * Elimina una imagen específica de la galería del producto.
     */
    public function removeImage(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $validated = $request->validate([
                'image_index' => ['required', 'integer', 'min:0']
            ]);
            
            $result = $this->removeProductImage($product, $validated['image_index']);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen', [
                'error' => $e->getMessage(),
                'product_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen'
            ], 500);
        }
    }

    /**
     * Obtiene el siguiente SKU disponible.
     */
    public function getNextSku()
    {
        return response()->json([
            'sku' => $this->skuGenerator->generate()
        ]);
    }

    /**
     * Verifica si un SKU está disponible.
     */
    public function checkSku(Request $request)
    {
        $sku = $request->query('sku');
        
        if (!$sku) {
            return response()->json(['available' => false]);
        }

        $exists = Product::where('sku', $sku)->exists();
        
        return response()->json([
            'available' => !$exists,
            'sku' => $sku
        ]);
    }

    /**
     * Verifica si un slug está disponible.
     */
    public function checkSlug(Request $request)
    {
        $slug = $request->query('slug');
        
        if (!$slug) {
            return response()->json(['available' => false]);
        }

        $exists = Product::where('slug', $slug)->exists();
        
        return response()->json([
            'available' => !$exists,
            'slug' => $slug
        ]);
    }

    /**
     * Elimina una imagen de la galería de un color.
     */
    public function removeColorGalleryImage(Request $request, $colorId)
    {
        try {
            $color = ProductColor::findOrFail($colorId);
            
            $validated = $request->validate([
                'image_index' => ['required', 'integer', 'min:0']
            ]);
            
            $result = $this->removeColorGallery($color, $validated['image_index']);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen de galería de color', [
                'error' => $e->getMessage(),
                'color_id' => $colorId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen'
            ], 500);
        }
    }

    /**
     * Elimina un color específico del producto.
     */
    public function destroyColor($colorId)
    {
        try {
            $color = ProductColor::findOrFail($colorId);
            
            $result = $this->deleteColor($color);
            
            return response()->json($result, $result['success'] ? 200 : 422);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar color', [
                'error' => $e->getMessage(),
                'color_id' => $colorId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el color'
            ], 500);
        }
    }
}