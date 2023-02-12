<template>
    <div>
        <cargando v-if="cargando" />
        <div class="card" v-else>
            <div class="card-body">
                <div class="mb-3" v-for="field in fields" :key="field.id">
                    <template v-if="field.editable">
                        <crud-field :field="field" :data="data" :url="url" :static_url="static_url" />
                    </template>
                </div>
            </div>
            <div class="card-footer text-right">
                <button class="btn btn-secondary" @click="regresar">
                    <i class="fas fa-chevron-left"></i> Regresar
                </button>
                <button class="btn btn-primary" @click="guardar"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["id", "url", "static_url"],
    mounted() {
        axios
            .get(this.url + "/" + this.id)
            .then((res) => {
                this.fields = res.data.fields;
                this.data = res.data.data;
                this.cargando = false;
            })
            .catch((error) => {
                // this.handleError(error);
            });
    },
    data() {
        return {
            cargando: true,
            fields: [],
            data: [],
        };
    },
    methods: {
        regresar() {
            window.location = this.url;
        },
        guardar() {
            axios
                .put(this.url + "/" + this.id, {
                    data: this.data,
                })
                .then((response) => {
                    window.location = this.url;
                })
                .catch((error) => {
                    // this.handleError(error);
                });
        },
    },
};
</script>
