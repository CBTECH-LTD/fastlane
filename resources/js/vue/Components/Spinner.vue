<template>
    <div class="spinner"></div>
</template>

<script>
    export default {
        name: 'Spinner',

        props: {
            color: {
                type: String,
                default: 'brand',
            }
        }
    }
</script>

<style scoped>
    .spinner {
        display: inline-block;
        width: 28px;
        height: 28px;
    }

    .spinner:after {
        content: " ";
        display: block;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 3px solid theme('colors.info.500');
        border-color: theme('colors.info.500') transparent theme('colors.info.500') transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }

    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
