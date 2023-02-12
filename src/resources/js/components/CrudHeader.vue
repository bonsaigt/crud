<template>
    <th scope="col" v-if="field.type == 'checkbox' || field.type == 'number'" class="text-center">
        {{ field.title }}
    </th>
    <th scope="col" v-else>
        {{ field.title }}
    </th>
</template>

<script>
export default {
    props: ["field"],
};
</script>
