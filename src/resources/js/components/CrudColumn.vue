<template>
    <td v-if="campo.tipo == 'checkbox'" class="text-center">
        <span v-if="fila[campo.columna] == 1" class="badge bg-success">Si</span>
        <span v-else class="badge bg-danger">No</span>
    </td>
    <td v-else-if="campo.tipo == 'number'" class="text-right">
        {{ fila[campo.columna] | numero }}
    </td>
    <td v-else-if="campo.tipo == 'combo'">
        {{
            fila[campo.relacion]
                ? fila[campo.relacion]["nombre"]
                : fila[campo.columna]
        }}
    </td>
    <td v-else-if="campo.tipo == 'url'">
        <a :href="full_url" target="_blank">{{ full_url }}</a>
    </td>
    <td v-else-if="campo.tipo == 'datetime'">
        {{ fila[campo.columna] | datetime }}
    </td>
    <td v-else-if="campo.tipo == 'date'">
        {{ fila[campo.columna] | date }}
    </td>
    <td v-else>
        {{ fila[campo.columna] }}
    </td>
</template>

<script>
export default {
    props: ["fila", "campo"],
    computed: {
        full_url() {
            if (this.campo.tipo == "url") {
                return this.campo["url"] + this.fila[this.campo.columna];
            }

            return "";
        },
    },
};
</script>
