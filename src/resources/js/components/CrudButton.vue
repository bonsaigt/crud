<template>
    <a
        :href="fullUrl"
        type="button"
        :class="`btn btn-sm btn-` + boton.tipo"
        :target="boton.target"
    >
        <i :class="boton.icono"></i>
    </a>
</template>

<script>
export default {
    props: ["boton", "fila"],
    computed: {
        fullUrl() {
            return this.boton.url.replace("{id}", this.fila.id);
        },
    },
};
</script>
