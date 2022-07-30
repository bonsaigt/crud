<template>
    <div>
        <cargando v-if="cargando" />
        <div class="card" v-else>
            <div class="card-body">
                <div class="mb-3" v-for="campo in campos" :key="campo.id">
                    <template v-if="campo.editable">
                        <crud-field
                            :campo="campo"
                            :dato="dato"
                            :url="url"
                            :static_url="static_url"
                        />
                    </template>
                </div>
            </div>
            <div class="card-footer text-right">
                <button class="btn btn-secondary" @click="regresar">
                    <i class="fas fa-chevron-left"></i> Regresar
                </button>
                <button class="btn btn-primary" @click="guardar">
                    <i class="fas fa-save"></i> Guardar
                </button>
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
                this.campos = res.data.campos;
                this.dato = res.data.dato;
                this.cargando = false;
            })
            .catch((error) => {
                // this.handleError(error);
            });
    },
    data() {
        return {
            cargando: true,
            campos: [],
            dato: [],
        };
    },
    methods: {
        regresar() {
            window.location = this.url;
        },
        guardar() {
            axios
                .put(this.url + "/" + this.id, {
                    dato: this.dato,
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
