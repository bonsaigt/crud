<template>
    <button v-if="link" type="button" class="btn btn-primary" :enabled="link.active" v-html="link.label" @click="page"></button>
</template>

<script>
export default {
    props: ["link"],
    methods: {
        page() {
            this.$parent.page(this.link.url)
        }
    }
};
</script>
