<template>
    <td v-if="field.type == 'checkbox'" class="text-center">
        <span v-if="row[field.column] == 1" class="badge bg-success">Si</span>
        <span v-else class="badge bg-danger">No</span>
    </td>
    <td v-else-if="field.type == 'number'" class="text-right">
        {{ row[field.column] | numero }}
    </td>
    <td v-else-if="field.type == 'combo'">
        {{ row[field.relacion] ? row[field.relacion]["nombre"] : row[field.column] }}
    </td>
    <td v-else-if="field.type == 'url'">
        <a v-if="row['url']" :href="row['url']" target="_blank">{{ field['tag'] ? field['tag'] : row['url'] }}</a>
    </td>
    <td v-else-if="field.type == 'datetime'">
        {{ row[field.column] | datetime }}
    </td>
    <td v-else-if="field.type == 'date'">
        {{ row[field.column] | date }}
    </td>
    <td v-else>
        {{ row[field.column] }}
    </td>
</template>

<script>
export default {
    props: ["row", "field"],
};
</script>
