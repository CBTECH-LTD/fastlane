<template>
    <div class="h-screen flex">
        <div class="flex items-center justify-center w-full md:w-1/2 bg-white px-8 py-12">
            <div class="max-w-md w-full p-8 rounded">
                <div class="flex flex-col justify-center items-center">
                    <img :src="$asset($page.app.assets.logoImage)" alt="" class="w-20 mb-8">
                    <h2 class="text-center text-3xl leading-9 font-extrabold text-gray-900">
                        {{ $l('core.login.title') }}
                    </h2>
                </div>

                <form @submit.prevent="attemptLogin" class="mt-8">
                    <transition
                        enter-active-class="transition duration-500 easy-in-out"
                        leave-active-class="transition duration-500 easy-in-out"
                        enter-class="opacity-0 scale-70"
                        enter-to-class="opacity-100 scale-100"
                        leave-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-70"
                    >
                        <div v-if="errors.length > 0" class="w-full mb-4 rounded border border-red-200 p-4 bg-red-100 text-red-500">
                            <div v-for="e in errors">
                                {{ e }}
                            </div>
                        </div>
                    </transition>

                    <f-form-field stacked>
                        <span slot="label">{{ $l('core.login.email') }}</span>
                        <input type="email" name="email" required autofocus class="form-input w-full" :placeholder="$l('core.login.email')" v-model="email">
                    </f-form-field>
                    <f-form-field stacked>
                        <span slot="label">{{ $l('core.login.password') }}</span>
                        <input type="password" name="password" required class="form-input w-full" :placeholder="$l('core.login.password')" v-model="password">
                    </f-form-field>
                    <div class="py-2 text-right text-sm leading-5">
                        <f-link as="a" href="#" color="gray">
                            {{ $l('core.login.forgot') }}
                        </f-link>
                    </div>
                    <div class="mt-2">
                        <f-button :loading="isAttemptingLogin" submit full size="lg" color="black">{{ $l('core.login.button') }}</f-button>
                    </div>
                </form>
            </div>
        </div>
        <div class="hidden md:block flex-grow h-full bg-white right-panel" :style="`background-image: url(${$asset($page.app.assets.loginBackground)})`"></div>
    </div>
</template>

<script>
export default {
    name: 'Login',

    props: {
        url: {
            type: String,
            required: true,
        }
    },

    data () {
        return {
            email: '',
            password: '',
            isAttemptingLogin: false,
        }
    },

    computed: {
        errors () {
            return this.$page ? this.$page.errors.get('email') : []
        }
    },

    methods: {
        async attemptLogin () {
            this.isAttemptingLogin = true

            await this.$inertia.post(this.url, {
                email: this.email,
                password: this.password,
            })

            this.isAttemptingLogin = false
        },
    }
}
</script>

<style scoped>
.right-panel {
    -webkit-box-shadow: 0px 0px 120px 12px rgba(0, 0, 0, 0.30);
    -moz-box-shadow: 0px 0px 120px 12px rgba(0, 0, 0, 0.30);
    box-shadow: 0px 0px 120px 12px rgba(0, 0, 0, 0.30);
    background-size: cover;
    background-repeat: no-repeat;
}
</style>
