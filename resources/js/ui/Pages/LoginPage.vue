<template>
  <Head title="Log in – Adventurous" />
  <div class="relative h-screen w-screen overflow-hidden">
    <img
      id="background_image"
      src="/images/5.7m.png"
      width="3200"
      alt=""
      class="login-bg-pan absolute top-0 h-full max-w-none object-cover"
    />
    <div class="flex h-screen w-screen flex-row items-center justify-center">
      <UContainer
        class="bg-primary-400 relative w-1/2 rounded-lg p-1 text-white shadow-lg shadow-black sm:py-6 lg:py-8"
      >
        <div class="mx-auto w-96">
          <img
            src="/images/adventurous_logo.png"
            alt="Adventurous"
            class="mx-auto mb-3 size-24"
          />
          <h1 class="sr-only">{{ t('Log in') }}</h1>
          <form class="flex flex-col gap-3" @submit.prevent="submit">
            <div>
              <label for="email" class="block text-start">{{
                t('Email')
              }}</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                name="email"
                class="bg-primary-300 m-auto block w-full rounded-lg border-2 border-stone-500 px-2 py-1 text-center outline-hidden"
              />
            </div>
            <div>
              <label for="password" class="block text-start">{{
                t('Password')
              }}</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                name="password"
                minlength="4"
                class="bg-primary-300 m-auto block w-full rounded-lg border-2 border-stone-500 px-2 py-1 text-start outline-hidden"
              />
            </div>
            <p :class="['text-red-600', { invisible: !form.errors.email }]">
              {{ t('Invalid credentials') }}
            </p>
            <UButton
              type="submit"
              :loading="form.processing"
              class="w-full max-w-md justify-center"
              color="primary"
            >
              {{ t('Log in') }}
            </UButton>
            <p>
              {{ t('Registration is currently closed, contact') }}
              kjetil@baksaas.no
              {{ t('for access') }}
            </p>
          </form>
        </div>
      </UContainer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
  email: '',
  password: '',
});

const submit = () => {
  form.post('/authenticate', {
    onFinish: () => form.reset('password'),
  });
};
</script>

<style scoped>
.login-bg-pan {
  animation: login-bg-pan 60s linear infinite alternate;
}

@keyframes login-bg-pan {
  to {
    transform: translateX(calc(-100% + 100vw));
  }
}
</style>
