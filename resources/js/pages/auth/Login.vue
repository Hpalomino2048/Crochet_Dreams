<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 flex items-center justify-center p-4 relative overflow-hidden">
        <Head title="Iniciar Sesión" />
        
        <!-- Animated background patterns -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-0 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Login card -->
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
                        Bienvenido de vuelta
                    </h1>
                    <p class="text-gray-600">Inicia sesión para continuar</p>
                </div>

                <div v-if="status" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">✓</span>
                        <span>{{ status }}</span>
                    </div>
                </div>

                <Form
                    v-bind="AuthenticatedSessionController.store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <div class="space-y-5">
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
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="email"
                                    placeholder="tu@email.com"
                                    class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                                />
                            </div>
                            <InputError :message="errors.email" class="text-sm" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <Label for="password" class="text-gray-700 font-semibold text-sm">
                                    Contraseña
                                </Label>
                                <TextLink 
                                    v-if="canResetPassword" 
                                    :href="request()" 
                                    class="text-sm text-purple-600 hover:text-purple-700 transition-colors font-medium hover:underline" 
                                    :tabindex="5"
                                >
                                    ¿Olvidaste tu contraseña?
                                </TextLink>
                            </div>
                            <div class="relative">
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all bg-white/50 backdrop-blur-sm pl-4 pr-4 py-3 rounded-xl"
                                />
                            </div>
                            <InputError :message="errors.password" class="text-sm" />
                        </div>

                        <!-- Remember me -->
                        <div class="flex items-center space-x-3 py-2">
                            <Checkbox 
                                id="remember" 
                                name="remember" 
                                :tabindex="3"
                                class="data-[state=checked]:bg-purple-600 data-[state=checked]:border-purple-600 rounded-md"
                            />
                            <Label for="remember" class="text-sm text-gray-600 cursor-pointer font-normal">
                                Mantener sesión iniciada
                            </Label>
                        </div>

                        <!-- Login button -->
                        <Button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 hover:from-purple-700 hover:via-purple-600 hover:to-pink-600 text-white font-semibold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" 
                            :tabindex="4" 
                            :disabled="processing" 
                            data-test="login-button"
                        >
                            <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                            <span class="text-base">{{ processing ? 'Iniciando sesión...' : 'Iniciar Sesión' }}</span>
                        </Button>
                    </div>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white/80 text-gray-500 font-medium">¿Nuevo en Crochet Dreams?</span>
                        </div>
                    </div>

                    <!-- Sign up link -->
                    <div class="text-center">
                        <TextLink 
                            :href="register()" 
                            :tabindex="5"
                            class="inline-flex items-center justify-center w-full px-6 py-3.5 border-2 border-purple-200 text-purple-700 font-semibold rounded-xl hover:bg-purple-50 hover:border-purple-300 transition-all duration-300"
                        >
                            Crear una cuenta nueva
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