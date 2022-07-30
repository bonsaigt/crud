<template>
    <div>
        <template v-if="campo.tipo != 'checkbox'">
            <label class="form-label">{{ campo.titulo }}</label>
            <textarea
                v-if="campo.tipo == 'textarea'"
                class="form-control"
                rows="5"
                v-model="dato[campo.columna]"
            ></textarea>
            <input
                v-else-if="campo.tipo == 'date'"
                class="form-control"
                type="date"
                v-model="dato[campo.columna]"
            />
            <input
                v-else-if="campo.tipo == 'datetime'"
                class="form-control"
                type="datetime-local"
                v-model="dato[campo.columna]"
            />
            <multiselect
                v-else-if="campo.tipo == 'combo'"
                v-model="dato[campo.relacion]"
                :options="campo.opciones"
                :searchable="true"
                :close-on-select="true"
                :show-labels="false"
                track-by="id"
                label="nombre"
                placeholder="Seleccione una opción"
            ></multiselect>
            <div
                class="row"
                v-else-if="campo.tipo == 'file' || campo.tipo == 'image'"
            >
                <div class="col-8">
                    <vue-dropzone
                        :id="`dz` + campo.id"
                        :options="dropzoneOptions"
                        @vdropzone-success="vsuccess"
                    ></vue-dropzone>
                </div>
                <div class="col-4">
                    <div
                        class="card"
                        v-if="
                            campo.tipo == 'image' &&
                            this.dato[this.campo.columna]
                        "
                    >
                        <img
                            :src="static_url + dato[campo.columna]"
                            class="card-img-top"
                            alt="Imagen"
                        />
                        <div class="card-body text-right">
                            <a href="#" class="btn btn-danger btn-sm">
                                <i
                                    class="fas fa-trash"
                                    @click="limpiarCampo"
                                ></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <input
                v-else-if="campo.tipo == 'number'"
                class="form-control"
                type="number"
                v-model="dato[campo.columna]"
            />
            <input
                v-else-if="campo.tipo == 'password'"
                class="form-control"
                type="password"
                v-model="dato[campo.columna]"
            />
            <input
                type="text"
                class="form-control"
                v-model="dato[campo.columna]"
                v-else
            />
        </template>
        <div class="form-check" v-else>
            <input
                type="checkbox"
                class="form-check-input"
                :id="campo.id"
                v-model="dato[campo.columna]"
            />
            <label class="form-check-label" :for="campo.id">{{
                campo.titulo
            }}</label>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";

export default {
    props: ["campo", "dato", "url", "static_url"],
    data() {
        return {
            dropzoneOptions: {
                url: this.url + "?path=" + this.campo.path,
                thumbnailWidth: 150,
                maxFilesize: 5,
                dictDefaultMessage:
                    "<i class='fas fa-cloud'></i> ARRASTRAR ARCHIVO",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            },
        };
    },
    methods: {
        vsuccess(file, response) {
            this.dato[this.campo.columna] = response;
        },
        limpiarCampo() {
            this.dato[this.campo.columna] = null;
        },
    },
    components: {
        Multiselect,
        vueDropzone: vue2Dropzone,
    },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
