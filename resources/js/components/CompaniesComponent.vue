<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-primary mb-3" v-on:click="showModalTambah">
                    Tambah
                </button>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" placeholder="Pencarian" v-model="pencarian">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
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
                            <td><img v-if="item.logo != null" style="width: 150px" class="img-thumbnail"
                                    v-bind:src="'http://127.0.0.1:8000/api/images?path=' + item.logo"></td>
                            <td><a target="_blank" :href="item.website">{{ item.website }}</a></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm"
                                        v-on:click="editCompany(item)">Ubah</button>
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
                            <button class="page-link" v-on:click="getCompanies(page - 1, pencarian)">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </button>
                        </li>
                        <li class="page-item" v-for="item in last_page" v-bind:class="{ 'active': item == page }">
                            <button class="page-link" v-on:click="getCompanies(item, pencarian)">{{ item }}</button>
                        </li>
                        <li class="page-item" v-bind:class="{ 'disabled': page == last_page }">
                            <a class="page-link" v-on:click="getCompanies(page + 1, pencarian)">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true" ref="modalTambah">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            v-on:click="closeModalTambah">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                v-bind:class="{ 'is-invalid': validate.name.length > 0 }" v-model="form.name"
                                name="name" id="name" placeholder="Input Name">
                            <small v-if="validate.name.length > 0" class="form-text text-danger">
                                {{ validate.name }}
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control"
                                v-bind:class="{ 'is-invalid': validate.email.length > 0 }" v-model="form.email"
                                name="email" id="email" placeholder="Input Email">
                            <small v-if="validate.email.length > 0" class="form-text text-danger">
                                {{ validate.email }}
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="file" class="form-control-file" name="logo" id="logo" ref="logo"
                                v-on:change="onChangeLogoTambah" placeholder="logo">
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="text" class="form-control"
                                v-bind:class="{ 'is-invalid': validate.website.length > 0 }" v-model="form.website"
                                name="website" id="website" placeholder="Input Website">
                            <small v-if="validate.website.length > 0" class="form-text text-danger">
                                {{ validate.website }}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-on:click="postCompany">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalUbah" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true" ref="modalUbah">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            v-on:click="closeModalUbah">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                v-bind:class="{ 'is-invalid': validate_ubah.name.length > 0 }" v-model="form_ubah.name"
                                name="name" id="name" placeholder="Input Name">
                            <small v-if="validate_ubah.name.length > 0" class="form-text text-danger">
                                {{ validate_ubah.name }}
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control"
                                v-bind:class="{ 'is-invalid': validate_ubah.email.length > 0 }"
                                v-model="form_ubah.email" name="email" id="email" placeholder="Input Email">
                            <small v-if="validate_ubah.email.length > 0" class="form-text text-danger">
                                {{ validate_ubah.email }}
                            </small>
                        </div>
                        <div class="form-group mb-1">
                            <label for="logo">Logo <small>(abaikan jika tidak ingin diubah)</small></label>
                            <input type="file" class="form-control-file" name="logo" id="logo" ref="logo"
                                v-on:change="onChangeLogoUbah" placeholder="logo">
                        </div>
                        <div class="form-check mt-0 mb-2">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="delete_logo" id="delete_logo"
                                    value="checkedValue" v-model="form_ubah.delete_logo" checked>
                                Hapus Logo
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="text" class="form-control"
                                v-bind:class="{ 'is-invalid': validate_ubah.website.length > 0 }"
                                v-model="form_ubah.website" name="website" id="website" placeholder="Input Website">
                            <small v-if="validate_ubah.website.length > 0" class="form-text text-danger">
                                {{ validate_ubah.website }}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-on:click="saveCompany">Simpan</button>
                    </div>
                </div>
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
                logo: '',
                website: '',
            },
            form_ubah: {
                name: '',
                email: '',
                logo: '',
                website: '',
                delete_logo: false,
            },
            validate: {
                name: [],
                email: [],
                website: [],
            },
            validate_ubah: {
                name: [],
                email: [],
                website: [],
            },
            pencarian: '',
            page: 1,
            last_page: 0,
            delayTimer: null,
        }
    },
    mounted() {
        this.getCompanies(this.page, this.pencarian);
    },
    watch: {
        pencarian(newPencarian, oldPencarian) {
            this.isLoading = true;
            clearTimeout(this.delayTimer);
            this.delayTimer = setTimeout(() => {
                this.getCompanies(1, newPencarian);
            }, 500);
        }
    },
    methods: {
        showModalTambah() {
            $(this.$refs.modalTambah).modal('show');
        },

        closeModalTambah() {
            $(this.$refs.modalTambah).modal('hide');
        },

        showModalUbah() {
            $(this.$refs.modalUbah).modal('show');
        },

        closeModalUbah() {
            $(this.$refs.modalUbah).modal('hide');
        },

        async getCompanies(page = 1, search = '') {
            this.page = page;
            this.pencarian = search;
            try {
                const res = await axios.get('/api/companies?page=' + page + '&search=' + search);
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

            this.validate.name = validate.single(this.form.name, { presence: { allowEmpty: false } }) ?? [];

            if (this.form.email != '') {
                this.validate.email = validate.single(this.form.email, { email: true }) ?? [];
            }

            if (this.form.website != '') {
                this.validate.website = validate.single(this.form.website, { url: true }) ?? [];
            }

            if (collect(this.validate).values().every(item => item.length == 0)) {
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

                    this.closeModalTambah();
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

        async editCompany(form) {
            this.form_ubah = { ...form, logo: '', website: form.website ?? '', delete_logo: false };
            this.showModalUbah();
        },

        async saveCompany() {
            this.validate_ubah.name = validate.single(this.form_ubah.name, { presence: { allowEmpty: false } }) ?? [];

            if (this.form_ubah.email != '') {
                this.validate_ubah.email = validate.single(this.form_ubah.email, { email: true }) ?? [];
            }

            if (this.form_ubah.website != '') {
                this.validate_ubah.website = validate.single(this.form_ubah.website, { url: true }) ?? [];
            }

            if (collect(this.validate_ubah).values().every(item => item.length == 0)) {
                try {
                    const form_ubah = new FormData();
                    collect(this.form_ubah).map((item, index) => {
                        form_ubah.append(index, item);
                    })
                    form_ubah.append('_method', 'PUT');
                    const res = await axios.post('/api/companies', form_ubah, { headers: { "Content-Type": "application/x-www-form-urlencoded" } });

                    this.getCompanies();

                    swal.fire(
                        'Information',
                        res.data.message,
                        'success'
                    )

                    this.form_ubah = {
                        name: '',
                        email: '',
                        logo: '',
                        website: '',
                    }
                    this.$refs.logo.value = '';

                    this.closeModalUbah();
                } catch (_) {
                    console.log(_);
                }
            }
        },
        onChangeLogoTambah(e) {
            this.form.logo = e.target.files[0] ?? '';
        },
        onChangeLogoUbah(e) {
            this.form_ubah.logo = e.target.files[0] ?? '';
        },
    }
}
</script>
