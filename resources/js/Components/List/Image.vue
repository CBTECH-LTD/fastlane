<template>
    <div class="w-full flex justify-center">
        <span class="block w-16 h-16 rounded-full shadow bg-no-repeat bg-cover" :style="backgroundStyles"></span>
    </div>
</template>

<script>
    import ListRenderer from '../Mixins/ListRenderer'

    export default {
        name: 'ListImage',
        mixins: [ListRenderer],

        computed: {
            backgroundStyles () {
                if (! this.value) {
                    return {}
                }

                return {
                    backgroundImage: `url(${this.value.url})`,
                }
            }
        },
    }
</script>
