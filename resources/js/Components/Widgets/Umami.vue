<template>
    <div>
        <iframe :src="url" frameborder="0" class="w-full" :style="style"></iframe>
    </div>
</template>

<script>
export default {
    name: 'UmamiWidget',
    inheritAttrs: false,
    props: {
        url: {
            type: String,
            required: true,
        },
        height: {
            type: String,
            required: true,
        },
    },
    computed: {
        style() {
            return {
                height: this.height,
            }
        }
    }
}
</script>
