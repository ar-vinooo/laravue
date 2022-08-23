<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-primary mb-2" v-on:click="showModal">
                    Tambah
                </button>

                <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                    aria-hidden="true" ref="modalTambah">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    v-on:click="closeModal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                        v-bind:class="{ 'is-invalid': validate.name != '' }" v-model="form.name"
                                        name="name" id="name" placeholder="Input Name">
                                    <small v-if="validate.name != ''" class="form-text text-danger">
                                        {{ validate.name }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control"
                                        v-bind:class="{ 'is-invalid': validate.email != '' }" v-model="form.email"
                                        name="email" id="email" placeholder="Input Email">
                                    <small v-if="validate.email != ''" class="form-text text-danger">
                                        {{ validate.email }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control-file" name="logo" id="logo" ref="logo"
                                        v-on:change="onChangeLogo" placeholder="logo">
                                </div>
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input type="text" class="form-control" v-model="form.website" name="website"
                                        id="website" placeholder="Input Website">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" v-on:click="postCompany">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped border">
                    <thead>
                        <tr>
                            <th>Index</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Logo</th>
                            <th>Website</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <td colspan="6" class="text-center">Loading...</td>
                        </tr>
                        <tr v-else v-for="item, index in companies">
                            <td>{{ (page * 10) - (10 - index - 1) }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.email }}</td>
                            <td><img style="width: 150px" class="img-thumbnail"
                                    v-bind:src="'http://127.0.0.1:8000/api/images?path=' + item.logo"></td>
                            <td><a target="_blank" :href="item.website">{{ item.website }}</a></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm">Ubah</button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        v-on:click="deleteCompany(item.id)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="companies.length == 0 && !isLoading">
                            <td colspan="6" class="text-center">Data Tidak Ada!</td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Page navigation" class="mt-2">
                    <ul class="pagination">
                        <li class="page-item" v-bind:class="{ 'disabled': page == 1 }">
                            <button class="page-link" v-on:click="getCompanies(page - 1)">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </button>
                        </li>
                        <li class="page-item" v-for="item in last_page" v-bind:class="{ 'active': item == page }">
                            <button class="page-link" v-on:click="getCompanies(item)">{{ item }}</button>
                        </li>
                        <li class="page-item" v-bind:class="{ 'disabled': page == last_page }">
                            <a class="page-link" v-on:click="getCompanies(page + 1)">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            isLoading: true,
            companies: [],
            form: {
                name: '',
                email: '',
                logo: null,
                website: '',
            },
            validate: {
                name: '',
                email: '',
            },
            page: 1,
            last_page: 0,
        }
    },
    mounted() {
        this.getCompanies(this.page);

    },
    methods: {
        showModal() {
            $(this.$refs.modalTambah).modal('show');
        },

        closeModal() {
            $(this.$refs.modalTambah).modal('hide');
        },

        async getCompanies(page = 1) {
            this.page = page;
            try {
                const res = await axios.get('/api/companies?page=' + page);
                const data = res.data.data.data;
                this.page = res.data.data.current_page;
                this.last_page = res.data.data.last_page;
                this.companies = data;
                this.isLoading = false;
            } catch (_) {
                console.log(_);
            }
        },

        async postCompany() {

            this.validate = {
                name: '',
                email: ''
            }

            if (this.form.name == '') {
                this.validate.name = 'name cannot be null!';
            }

            if (this.form.email != '') {
                if (!this.form.email.includes('@') || !this.form.email.includes('.')) {
                    this.validate.email = 'email invalid!';
                }
            }

            if (collect(this.validate).values().every(item => item == '')) {
                try {
                    const form = new FormData();
                    collect(this.form).map((item, index) => {
                        form.append(index, item);
                    })

                    const res = await axios.post('/api/companies', form, { headers: { "Content-Type": "multipart/form-data" } });

                    this.getCompanies();

                    swal.fire(
                        'Information',
                        res.data.message,
                        'success'
                    )

                    this.form = {
                        name: '',
                        email: '',
                        logo: '',
                        website: '',
                    }
                    this.$refs.logo.value = '';

                    this.closeModal();
                } catch (_) {
                    console.log(_);
                }
            }
        },

        async deleteCompany(id) {
            swal.fire({
                title: 'Information',
                text: "Apakah kamu yakin ingin menghapus data?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yakin!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await axios.delete('/api/companies/' + id);
                        this.getCompanies();

                        swal.fire(
                            'Information',
                            res.data.message,
                            'success'
                        )
                    } catch (_) {
                        console.log(_);
                    }
                }
            })



        },
        onChangeLogo(e) {
            this.form.logo = e.target.files[0];
        }
    }
}
</script>
