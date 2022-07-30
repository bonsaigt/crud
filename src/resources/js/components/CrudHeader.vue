<template>
    <th
        scope="col"
        v-if="campo.tipo == 'checkbox' || campo.tipo == 'number'"
        class="text-center"
    >
        {{ campo.titulo }}
    </th>
    <th scope="col" v-else>
        {{ campo.titulo }}
    </th>
</template>

<script>
export default {
    props: ["campo"],
};
</script>
