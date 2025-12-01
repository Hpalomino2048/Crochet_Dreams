<script setup lang="ts">
import PasswordResetLinkController from '@/actions/App/Http/Controllers/Auth/PasswordResetLinkController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 flex items-center justify-center p-4 relative overflow-hidden">
        <Head title="Recuperar Contraseña" />
        
        <!-- Animated background patterns -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-0 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Forgot password card -->
        <div class="relative w-full max-w-md">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 md:p-10 border border-white/50">
                <!-- Logo o ícono -->
                <div class="flex justify-center mb-8">
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-600 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-xl transform hover:scale-105 transition-transform duration-300">
                            <span class="text-4xl">💌</span>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-yellow-400 to-orange-400 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- Title -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                        ¿Olvidaste tu contraseña?
                    </h1>
                    <p class="text-gray-600">No te preocupes, te enviaremos instrucciones para recuperarla</p>
                </div>

                <!-- Success message -->
                <div v-if="status" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="text-sm text-green-800">
                            <p class="font-medium mb-1">¡Correo enviado!</p>
                            <p class="text-green-700">{{ status }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <Form v-bind="PasswordResetLinkController.store.form()" v-slot="{ errors, processing }" class="space-y-5">
                        <!-- Email -->
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
                                    autofocus 
                                    placeholder="tu@email.com"
                                    class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                                />
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                            </div>
                            <InputError :message="errors.email" class="text-sm" />
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
                                    <p class="font-medium mb-1">Revisa tu bandeja de entrada</p>
                                    <p class="text-blue-700">Te enviaremos un enlace seguro para restablecer tu contraseña. Si no lo ves, revisa tu carpeta de spam.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <Button 
                            class="w-full bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 hover:from-purple-700 hover:via-purple-600 hover:to-pink-600 text-white font-semibold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" 
                            :disabled="processing" 
                            data-test="email-password-reset-link-button"
                        >
                            <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                            <span class="text-base">{{ processing ? 'Enviando enlace...' : 'Enviar Enlace de Recuperación' }}</span>
                        </Button>
                    </Form>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white/80 text-gray-500 font-medium">¿Recordaste tu contraseña?</span>
                        </div>
                    </div>

                    <!-- Back to login -->
                    <div class="text-center">
                        <TextLink 
                            :href="login()"
                            class="inline-flex items-center justify-center w-full px-6 py-3.5 border-2 border-purple-200 text-purple-700 font-semibold rounded-xl hover:bg-purple-50 hover:border-purple-300 transition-all duration-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Volver a Iniciar Sesión
                        </TextLink>
                    </div>
                </div>
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