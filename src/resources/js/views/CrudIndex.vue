<template>
    <div class="card">
        <div class="card-body text-right">
            <a :href="url + `/create`" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo
            </a>
        </div>
        <div class="card-body">
            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <crud-header
                            v-for="campo in campos"
                            :key="campo.id"
                            :campo="campo"
                        />
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="fila in datos" :key="fila.id">
                        <crud-column
                            v-for="campo in campos"
                            :key="campo.id"
                            :fila="fila"
                            :campo="campo"
                        />
                        <td class="text-right">
                            <div class="btn-group" role="group">
                                <crud-button
                                    v-for="(boton, i) in botones"
                                    :key="i"
                                    :boton="boton"
                                    :fila="fila"
                                />
                                <a
                                    :href="url + `/` + fila.id + `/edit`"
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    @click="borrar(fila['id'])"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ["campos", "datos", "url", "botones"],
    methods: {
        borrar(fila_id) {
            if (
                confirm("¿Está seguro que desea eliminar este registro?") ==
                true
            ) {
                axios
                    .delete(this.url + "/" + fila_id)
                    .then((response) => {
                        window.location = this.url;
                    })
                    .catch((error) => {
                        // this.handleError(error);
                    });
            }
        },
    },
};
</script>
