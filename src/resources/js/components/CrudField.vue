<template>
    <div>
        <template v-if="field.type != 'checkbox'">
            <label class="form-label">{{ field.title }}</label>
            <textarea
                v-if="field.type == 'textarea'"
                class="form-control"
                rows="5"
                v-model="data[field.column]"
            ></textarea>
            <input v-else-if="field.type == 'date'" class="form-control" type="date" v-model="data[field.column]" />
            <input
                v-else-if="field.type == 'datetime'"
                class="form-control"
                type="datetime-local"
                v-model="data[field.column]"
            />
            <multiselect
                v-else-if="field.type == 'combo'"
                v-model="data[field.relacion]"
                :options="field.opciones"
                :searchable="true"
                :close-on-select="true"
                :show-labels="false"
                track-by="id"
                label="nombre"
                placeholder="Seleccione una opción"
            ></multiselect>
            <div class="row" v-else-if="field.type == 'file' || field.type == 'image'">
                <div class="col-8">
                    <vue-dropzone
                        :id="`dz` + field.id"
                        :options="dropzoneOptions"
                        @vdropzone-success="vsuccess"
                    ></vue-dropzone>
                </div>
                <div class="col-4">
                    <div class="card" v-if="field.type == 'image' && this.data[this.field.column]">
                        <img :src="static_url + data[field.column]" class="card-img-top" alt="Imagen" />
                        <div class="card-body text-right">
                            <a href="#" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash" @click="clearField"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <input v-else-if="field.type == 'number'" class="form-control" type="number" v-model="data[field.column]" />
            <input
                v-else-if="field.type == 'password'"
                class="form-control"
                type="password"
                v-model="data[field.column]"
            />
            <input type="text" class="form-control" v-model="data[field.column]" v-else />
        </template>
        <div class="form-check" v-else>
            <input type="checkbox" class="form-check-input" :id="field.id" v-model="data[field.column]" />
            <label class="form-check-label" :for="field.id">{{ field.title }}</label>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";

export default {
    props: ["field", "data", "url", "static_url"],
    data() {
        return {
            dropzoneOptions: {
                url: this.url + "?path=" + this.field.path,
                thumbnailWidth: 150,
                maxFilesize: 5,
                dictDefaultMessage: "<i class='fas fa-cloud'></i> ARRASTRAR ARCHIVO",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
            },
        };
    },
    methods: {
        vsuccess(file, response) {
            this.data[this.field.column] = response;
        },
        clearField() {
            this.data[this.field.column] = null;
        },
    },
    components: {
        Multiselect,
        vueDropzone: vue2Dropzone,
    },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
