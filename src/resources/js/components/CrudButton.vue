<template>
    <a :href="fullUrl" type="button" :class="`btn btn-sm btn-` + button.type" :target="button.target">
        <i :class="button.icono"></i>
    </a>
</template>

<script>
export default {
    props: ["button", "row"],
    computed: {
        fullUrl() {
            return this.button.url.replace("{id}", this.row.id);
        },
    },
};
</script>
