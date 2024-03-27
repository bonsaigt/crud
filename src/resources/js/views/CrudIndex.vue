<template>
    <div class="card">
        <div class="card-body text-right">
            <a :href="url + `/create`" class="btn btn-success"> <i class="fas fa-plus"></i> Nuevo </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-4">
                    <div class="input-group mb-3">
                        <input type="text" v-model="term" class="form-control" placeholder="Buscar" aria-label="Buscar" aria-describedby="button-addon2">
                        <button class="btn btn-outline-secondary" type="button" id="button-addon2" @click="search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <button class="btn btn-outline-secondary" type="button" @click="reset">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <crud-header v-for="field in fields" :key="field.id" :field="field" />
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in filtered" :key="row.id">
                        <crud-column v-for="field in fields" :key="field.id" :row="row" :field="field" />
                        <td class="text-right">
                            <div class="btn-group" role="group">
                                <crud-button v-for="(button, i) in buttons" :key="i" :button="button" :row="row" />
                                <a :href="url + `/` + row.id + `/edit`" type="button" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" @click="remove(row['id'])">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="btn-group mt-5" role="group" v-if="pager">
                <pager-button v-for="(link, i) of pager.links" :key="i" :link="link" @page="page" />
            </div>
        </div>
    </div>
</template>

<script>
import PagerButton from '../components/PagerButton.vue';
export default {
    components: { PagerButton },
    // props: ["fields", "data", "url", "buttons"],
    props: ["url"],
    data() {
        return {
            term: "",
            filtered: [],
            pager: null,
            fields: [],
            data: [],
            buttons: []
        };
    },
    mounted() {
        axios
            .get(this.url + "/data")
            .then((response) => {
                this.fields = response.data.fields;
                this.pager = response.data.data;
                this.data = response.data.data.data;
                this.filtered = response.data.data.data;
                this.buttons = response.data.buttons;
            })
            .catch((error) => {
                // this.handleError(error);
            });
    },
    methods: {
        search() {
            if (this.term != "") {
                let cols = this.fields
                    .filter((field) => {
                        return field.visible;
                    })
                    .map((field) => {
                        return field.column;
                    });

                this.filtered = this.data.filter((row) => {
                    let found = false;

                    cols.forEach((col) => {
                        if (row[col]) {
                            let column = row[col].toString();
                            column = column.toLowerCase();

                            if (column.indexOf(this.term.toLowerCase()) >= 0) {
                                found = true;
                            }
                        }
                    });

                    return found;
                });
            } else {
                this.filtered = this.data;
            }
        },
        reset() {
            this.filtered = this.data;
            this.term = ""
        },
        page(url) {
            if (url) {
                axios
                .get(url)
                .then((response) => {
                    this.pager = response.data.data;
                    this.data = response.data.data.data;
                    this.filtered = response.data.data.data;
                })
                .catch((error) => {
                    // this.handleError(error);
                }); 
            }
        },
        remove(row_id) {
            if (confirm("¿Está seguro que desea eliminar este registro?") == true) {
                axios
                    .delete(this.url + "/" + row_id)
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
