<template>
    <div class="h-screen flex">
        <div class="flex items-center justify-center w-full bg-white px-8 py-12">
            <div class="max-w-md w-full p-8 rounded">
                <div class="flex flex-col justify-center items-center">
                    <img :src="$asset($page.app.assets.logoImage)" alt="" class="w-20 mb-8">
                    <h2 class="text-center text-xl leading-9 font-extrabold text-gray-900">
                        Please confirm your password before continuing.
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
                        <span slot="label">Password</span>
                        <input type="password" name="password" required class="form-input w-full" placeholder="Password" v-model="password">
                    </f-form-field>
                    <div class="py-2 text-right text-sm leading-5">
                        <f-link as="a" href="#" color="gray">
                            Forgot your password?
                        </f-link>
                    </div>
                    <div class="mt-2">
                        <f-button :loading="isProcessing" submit full size="lg" color="black">Confirm password</f-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Auth.ConfirmPassword',

    props: {
        url: {
            type: String,
            required: true,
        }
    },

    data () {
        return {
            password: '',
            isProcessing: false,
        }
    },

    computed: {
        errors () {
            return this.$page ? this.$page.errors.get('email') : []
        }
    },

    methods: {
        async attemptLogin () {
            this.isProcessing = true

            await this.$inertia.post(this.url, {
                password: this.password,
            })

            this.isProcessing = false
        },
    }
}
</script>
