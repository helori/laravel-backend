<style scoped>
nav{
    margin: 0 0 15px 0;
} 
.pagination{
    margin: 0;
}
</style>

<template>
    <nav aria-label="Page navigation" class="mb-0 d-block" v-show="pagination.total > pagination.per_page">
        <ul class="pagination"
            :class="{
                'justify-content-center': align === 'center',
                'justify-content-start': align === 'left',
                'justify-content-end': align === 'right',
            }">

            <li class="page-item" 
                :class="{
                    'disabled': pagination.current_page == 1
                }">
                <a class="page-link" @click="setPage(pagination.current_page - 1)" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <li class="page-item">
                <a class="page-link" @click="setPage(1)" v-if="pagination.current_page >= 4">1</a>
            </li>
            
            <li class="page-item disabled" v-if="pagination.current_page >= 5">
                <a class="page-link">...</a>
            </li>

            <li class="page-item" 
                v-for="p in pagination.last_page" 
                v-if="p >= pagination.current_page - 2 && p <= pagination.current_page + 2"
                :class="{
                    'active': p === pagination.current_page
                }">
                <a class="page-link" @click="setPage(p)">{{ p }}</a>
            </li>

            <li class="page-item disabled" v-if="pagination.current_page <= pagination.last_page - 4">
                <a class="page-link">...</a>
            </li>

            <li class="page-item"
                v-if="pagination.current_page <= pagination.last_page - 3">
                <a class="page-link" 
                @click="setPage(pagination.last_page)">
                {{ pagination.last_page }}</a>
            </li>

            <li class="page-item" 
                :class="{
                    'disabled': pagination.current_page === pagination.last_page
                }">
                <a class="page-link" @click="setPage(pagination.current_page + 1)" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>

        </ul>
    </nav>
</template>

<script>
    export default {
        props: {
            pagination: {
                type: Object,
                required: true
            },
            align: {
                type: String,
                required: false,
                default: 'center'
            }
        },

        methods: {
            setPage(page){
                this.$emit('change', page);
            }
        }
    }
</script>
