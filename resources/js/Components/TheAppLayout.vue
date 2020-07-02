<template>
    <div class="flex flex-col">
        <div class="fixed top-0 w-full h-20 bg-white shadow-lg z-40 flex justify-between">
            <!-- App logo -->
            <div>
                <img :src="$asset('img/app-logo.png')" alt="" class="h-20">
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
        <div class="mt-20 w-full flex-grow flex">
            <div class="flex flex-col border-r border-gray-300 bg-gray-200 h-screen sticky top-0 overflow-hidden" style="width: 320px">
                <!-- Navigation items -->
                <div class="flex-grow overflow-y-auto overflow-x-hidden custom-scroll">
                    <f-navigation-list @click="onNavigationItemClicked"/>
                </div>
            </div>

            <!-- Main area -->
            <div class="flex-1 flex flex-col">
                <div class="flex-1 p-12">
                    <div class="flex items-center justify-between pt-4 pb-8">
                        <div class="div">
                            <h1 class="text-3xl text-gray-700 font-extrabold">
                                <slot name="title"/>
                            </h1>
                            <div class="text-lg text-gray-500 font-semibold">
                                <slot name="subtitle"/>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <slot name="actions"/>
                        </div>
                    </div>

                    <slot/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'AppLayout',

        data () {
            return {
                isSigningOut: false,
                sidebar: {
                    show: false,
                    title: '',
                    icon: '',
                    description: null,
                    items: [],
                }
            }
        },

        computed: {
            flashMessageClasses () {
                if (!this.$page.flashMessage) {
                    return ''
                }

                if (this.$page.flashMessage.type === 'success') {
                    return 'text-green-700 bg-green-200 border-green-300'
                }

                if (this.$page.flashMessage.type === 'alert') {
                    return 'text-yellow-700 bg-yellow-200 border-yellow-300'
                }

                if (this.$page.flashMessage.type === 'info') {
                    return 'text-purple-700 bg-purple-200 border-purple-300'
                }

                return ''
            },
        },

        methods: {
            onNavigationItemClicked ({ item }) {
                if (item.type === 'group') {
                    this.sidebar.title = item.label
                    this.sidebar.icon = item.icon
                    this.sidebar.items = item.children
                    this.sidebar.show = true
                    return
                }

                this.sidebar.show = false
                this.sidebar.title = ''
                this.sidebar.icon = ''
                this.sidebar.items = []
            },

            async signOut () {
                this.isSigningOut = true
                await this.$inertia.post('/cp/logout')
                this.isSigningOut = false
            }
        }
    }
</script>
