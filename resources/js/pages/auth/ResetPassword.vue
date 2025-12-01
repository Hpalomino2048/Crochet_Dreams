<script setup lang="ts">
import NewPasswordController from '@/actions/App/Http/Controllers/Auth/NewPasswordController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 flex items-center justify-center p-4 relative overflow-hidden">
        <Head title="Restablecer Contraseña" />
        
        <!-- Animated background patterns -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-0 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Reset password card -->
        <div class="relative w-full max-w-md">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 md:p-10 border border-white/50">
                <!-- Logo o ícono -->
                <div class="flex justify-center mb-8">
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-600 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-xl transform hover:scale-105 transition-transform duration-300">
                            <span class="text-4xl">🔑</span>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-yellow-400 to-orange-400 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- Title -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                        Restablecer Contraseña
                    </h1>
                    <p class="text-gray-600">Crea una nueva contraseña segura para tu cuenta</p>
                </div>

                <Form
                    v-bind="NewPasswordController.store.form()"
                    :transform="(data) => ({ ...data, token, email })"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="space-y-5"
                >
                    <!-- Email (readonly) -->
                    <div class="space-y-2">
                        <Label for="email" class="text-gray-700 font-semibold text-sm">
                            Correo Electrónico
                        </Label>
                        <div class="relative">
                            <Input 
                                id="email" 
                                type="email" 
                                name="email" 
                                autocomplete="email" 
                                v-model="inputEmail" 
                                readonly
                                class="border-gray-200 bg-gray-50/50 text-gray-500 cursor-not-allowed backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                            />
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <InputError :message="errors.email" class="text-sm" />
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <Label for="password" class="text-gray-700 font-semibold text-sm">
                            Nueva Contraseña
                        </Label>
                        <div class="relative">
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                autofocus
                                placeholder="Mínimo 8 caracteres"
                                class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                            />
                        </div>
                        <InputError :message="errors.password" class="text-sm" />
                        <p class="text-xs text-gray-500 flex items-start gap-1 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Usa al menos 8 caracteres con una combinación de letras, números y símbolos</span>
                        </p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <Label for="password_confirmation" class="text-gray-700 font-semibold text-sm">
                            Confirmar Nueva Contraseña
                        </Label>
                        <div class="relative">
                            <Input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                placeholder="Confirma tu nueva contraseña"
                                class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                            />
                        </div>
                        <InputError :message="errors.password_confirmation" class="text-sm" />
                    </div>

                    <!-- Info box -->
                    <div class="bg-blue-50/50 border border-blue-200 rounded-xl p-4 backdrop-blur-sm">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Por tu seguridad</p>
                                <p class="text-blue-700">Una vez cambiada tu contraseña, serás redirigido a iniciar sesión con tus nuevas credenciales.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Reset button -->
                    <Button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 hover:from-purple-700 hover:via-purple-600 hover:to-pink-600 text-white font-semibold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" 
                        :disabled="processing" 
                        data-test="reset-password-button"
                    >
                        <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                        <span class="text-base">{{ processing ? 'Restableciendo...' : 'Restablecer Contraseña' }}</span>
                    </Button>
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