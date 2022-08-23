<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        I'm an example component.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            companies: []
        }
    },
    methods: {
        async getCompanies() {
            try {
                const res = await axios.get('/api/companies');
                console.log(res);
            } catch (_) {

            }
        }
    }
}
</script>
