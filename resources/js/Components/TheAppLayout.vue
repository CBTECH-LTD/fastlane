<template>
    <div class="flex flex-col">
        <div class="fixed top-0 w-full h-20 bg-white border-b border-gray-200 z-40 flex justify-between">
            <!-- App logo -->
            <div class="h-20 p-2">
                <img :src="$asset($page.app.assets.logoImage)" alt="" class="h-full">
            </div>

            <!-- User / Sign out -->
            <div class="flex items-center p-1 border-t border-gray-300">
                <div class="text-xs mr-16">
                    <strong class="block font-semibold text-gray-800">{{ $page.auth.user.attributes.name }}</strong>
                    <span class="block font-normal text-gray-600">{{ $page.auth.user.attributes.email }}</span>
                </div>
                <f-button @click="signOut" :loading="isSigningOut" variant="minimal" color="black" size="lg" class="text-2xl">
                    <f-icon name="sign-out"/>
                </f-button>
            </div>
        </div>
        <div class="mt-20 w-full flex">
            <div class="flex flex-col h-screen sticky overflow-hidden" style="width: 320px; top: 80px;">
                <!-- Navigation items -->
                <div class="flex-grow overflow-y-auto overflow-x-hidden custom-scroll">
                    <f-navigation-list class="my-6 px-2" :items="this.$page.menu"/>
                </div>
            </div>

            <!-- Main area -->
            <div class="w-full flex flex-row overflow-x-hidden px-4">
                <div v-if="$slots.hasOwnProperty('page-sidebar')">
                    <slot name="page-sidebar"/>
                </div>
                <div class="flex-grow mb-8" style="border-radius: 2rem">
                    <div v-if="$slots.hasOwnProperty('title')" ref="sticky" class="title-bar-wrapper" :class="stickyBarClass">
                        <div class="title-bar">
                            <div class="w-4/5">
                                <h1 class="title-bar__title">
                                    <slot name="title"/>
                                </h1>
                                <div class="title-bar__subtitle">
                                    <slot name="subtitle"/>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <slot name="actions"/>
                            </div>
                        </div>
                    </div>

                    <div v-if="$page.flashMessages && $page.flashMessages.length" class="mt-8 mb-4 px-12">
                        <div v-for="msg in $page.flashMessages" class="flash-message" :class="`flash-message--type-${msg.type}`">
                            <div v-if="msg.icon" class="mr-8 text-4xl">
                                <f-icon :name="msg.icon"></f-icon>
                            </div>
                            <div class="mt-4 text-sm font-normal tracking-wide leading-relaxed">
                                {{ msg.message }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 mb-12 px-12">
                        <slot/>
                    </div>
                </div>
            </div>
        </div>

        <portal-target name="modals">

        </portal-target>
    </div>
</template>

<script>

export default {
    name: 'AppLayout',

    data () {
        return {
            isSigningOut: false,
            stickyBarClass: '',
        }
    },

    methods: {
        async signOut () {
            this.isSigningOut = true
            await this.$inertia.post('/cp/logout')
            this.isSigningOut = false
        },

        observeStickyTitleBar ($observable) {
            let observer = new IntersectionObserver(entries => {
                this.stickyBarClass = (!entries[0].isIntersecting)
                    ? 'is-floating'
                    : ''
            }, {
                rootMargin: '-20px 0px',
                threshold: 1
            })

            observer.observe($observable)
        }
    },

    mounted () {
        if (this.$refs.sticky) {
            this.observeStickyTitleBar(this.$refs.sticky)
        }
    },
}
</script>

<style scoped>
.title-bar-wrapper {
    @apply sticky top-0 py-8 z-30 transition-all duration-300 overflow-hidden;
    border-top-left-radius: 2rem;
    border-top-right-radius: 2rem;
}

.title-bar {
    @apply flex items-center justify-between px-8 py-8 z-30 transition-all duration-300;
    height: 72px;
    top: 80px;
    transform: translateY(0);
}

.title-bar__title {
    @apply text-3xl text-gray-800 font-extrabold;
}

.title-bar__subtitle {
    @apply text-lg text-gray-600 font-semibold;
}

.title-bar-wrapper.is-floating {
    @apply pb-4 bg-white border-b border-brand-200  shadow-lg;
}

.title-bar-wrapper.is-floating .title-bar {
    @apply py-6 mb-12;
    transform: translateY(52px);
}

.title-bar-wrapper.is-floating .title-bar__title {
    /* @apply text-xl; */
}

.title-bar-wrapper.is-floating .title-bar__subtitle {
    /* @apply text-sm; */
}

.flash-message {
    @apply mb-2 p-2 border-l-4 flex items-start;
}

.flash-message--type-success {
    @apply text-green-700 bg-green-200 border-green-700;
}

.flash-message--type-alert {
    @apply text-yellow-700 bg-yellow-200 border-yellow-700;
}

.flash-message--type-danger {
    @apply text-red-700 bg-red-200 border-red-700;
}

.flash-message--type-info {
    @apply text-purple-700 bg-purple-200 border-purple-300;
}
</style>
