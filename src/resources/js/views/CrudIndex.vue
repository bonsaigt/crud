<template>
    <div class="card">
        <div class="card-body text-right">
            <a :href="url + `/create`" class="btn btn-success"> <i class="fas fa-plus"></i> Nuevo </a>
        </div>
        <div class="card-body">
            <label for="search" class="form-label">Buscar</label>
            <input type="text" class="form-control" id="search" @input="search" v-model="term" />
            <br />

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
        </div>
    </div>
</template>

<script>
export default {
    props: ["fields", "data", "url", "buttons"],
    data() {
        return {
            term: "",
            filtered: this.data,
        };
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
                        let column = row[col].toString();
                        column = column.toLowerCase();

                        if (column.indexOf(this.term.toLowerCase()) >= 0) {
                            found = true;
                        }
                    });

                    return found;
                });
            } else {
                this.filtered = this.data;
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
