<template>
    <div>
        <iframe :src="url" frameborder="0" class="w-full"></iframe>
    </div>
</template>

<script>
export default {
    name: 'UmamiWidget',
    props: {
        url: {
            type: String,
            required: true,
        }
    }
}
</script>

<style scoped>

</style>
