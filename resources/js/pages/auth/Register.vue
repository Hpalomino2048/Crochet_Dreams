<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 flex items-center justify-center p-4 relative overflow-hidden">
        <Head title="Crear Cuenta" />
        
        <!-- Animated background patterns -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-0 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Register card -->
        <div class="relative w-full max-w-md">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 md:p-10 border border-white/50">
                <!-- Logo o ícono -->
                <div class="flex justify-center mb-8">
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-600 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-xl transform hover:scale-105 transition-transform duration-300">
                            <span class="text-4xl">🧶</span>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-yellow-400 to-orange-400 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- Title -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                        Únete a Crochet Dreams
                    </h1>
                    <p class="text-gray-600">Crea tu cuenta y comienza a tejer tus sueños</p>
                </div>

                <Form
                    v-bind="RegisteredUserController.store.form()"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="space-y-5"
                >
                    <!-- Name -->
                    <div class="space-y-2">
                        <Label for="name" class="text-gray-700 font-semibold text-sm">
                            Nombre Completo
                        </Label>
                        <div class="relative">
                            <Input 
                                id="name" 
                                type="text" 
                                required 
                                autofocus 
                                :tabindex="1" 
                                autocomplete="name" 
                                name="name" 
                                placeholder="Tu nombre completo"
                                class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                            />
                        </div>
                        <InputError :message="errors.name" class="text-sm" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email" class="text-gray-700 font-semibold text-sm">
                            Correo Electrónico
                        </Label>
                        <div class="relative">
                            <Input 
                                id="email" 
                                type="email" 
                                required 
                                :tabindex="2" 
                                autocomplete="email" 
                                name="email" 
                                placeholder="tu@email.com"
                                class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                            />
                        </div>
                        <InputError :message="errors.email" class="text-sm" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <Label for="password" class="text-gray-700 font-semibold text-sm">
                            Contraseña
                        </Label>
                        <div class="relative">
                            <Input 
                                id="password" 
                                type="password" 
                                required 
                                :tabindex="3" 
                                autocomplete="new-password" 
                                name="password" 
                                placeholder="Mínimo 8 caracteres"
                                class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                            />
                        </div>
                        <InputError :message="errors.password" class="text-sm" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <Label for="password_confirmation" class="text-gray-700 font-semibold text-sm">
                            Confirmar Contraseña
                        </Label>
                        <div class="relative">
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                :tabindex="4"
                                autocomplete="new-password"
                                name="password_confirmation"
                                placeholder="Confirma tu contraseña"
                                class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                            />
                        </div>
                        <InputError :message="errors.password_confirmation" class="text-sm" />
                    </div>

                    <!-- Terms disclaimer -->
                    <div class="pt-2 pb-2">
                        <p class="text-xs text-gray-500 text-center leading-relaxed">
                            Al crear una cuenta, aceptas nuestros
                            <a href="#" class="text-purple-600 hover:text-purple-700 font-medium hover:underline">Términos de Servicio</a>
                            y
                            <a href="#" class="text-purple-600 hover:text-purple-700 font-medium hover:underline">Política de Privacidad</a>
                        </p>
                    </div>

                    <!-- Register button -->
                    <Button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 hover:from-purple-700 hover:via-purple-600 hover:to-pink-600 text-white font-semibold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" 
                        tabindex="5" 
                        :disabled="processing" 
                        data-test="register-user-button"
                    >
                        <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                        <span class="text-base">{{ processing ? 'Creando cuenta...' : 'Crear Cuenta' }}</span>
                    </Button>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white/80 text-gray-500 font-medium">¿Ya tienes una cuenta?</span>
                        </div>
                    </div>

                    <!-- Login link -->
                    <div class="text-center">
                        <TextLink 
                            :href="login()" 
                            class="inline-flex items-center justify-center w-full px-6 py-3.5 border-2 border-purple-200 text-purple-700 font-semibold rounded-xl hover:bg-purple-50 hover:border-purple-300 transition-all duration-300" 
                            :tabindex="6"
                        >
                            Iniciar Sesión
                        </TextLink>
                    </div>
                </Form>
            </div>

            <!-- Trust badges -->
            <div class="mt-8 text-center">
                <div class="flex items-center justify-center gap-6 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <span class="text-green-500">🔒</span>
                        <span>Conexión segura</span>
                    </div>
                    <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-blue-500">🛡️</span>
                        <span>Datos protegidos</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animación de blobs flotantes */
@keyframes blob {
    0%, 100% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Efecto glassmorphism mejorado */
.backdrop-blur-xl {
    backdrop-filter: blur(20px);
}
</style>