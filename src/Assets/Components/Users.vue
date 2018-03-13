<style scoped lang="scss">

</style>

<template>
    <div>

    	<div class="row">
    		<div class="col-sm-10">
    			<h1>Administrateurs</h1>
    		</div>
    		<div class="col-sm-2">
    			<button class="btn btn-primary btn-block" @click="openCreate">
					Nouveau...
				</button>
    		</div>
    	</div>
    	

    	<div class="user" v-show="create_open">
        	<div class="form-row">
				
				<div class="form-group col-md-3">
					<input type="text" class="form-control" v-model="create_item.name" id="name" placeholder="Nom...">
				</div>

				<div class="form-group col-md-3">
					<input type="email" class="form-control" v-model="create_item.email" id="email" placeholder="Email..." autocomplete="off">
				</div>

				<div class="form-group col-md-3">
					<input type="password" class="form-control" v-model="create_item.password" id="password" placeholder="Mot de passe...">
				</div>

				<div class="form-group col-md-3">
					<button class="btn btn-primary btn-block" @click="create()">
						Enregistrer
					</button>
				</div>

			</div>
			<div class="alert alert-danger" v-if="create_error">
				{{ create_error.message }}
			</div>
        </div>
        
        <div class="user" v-for="user in users" :key="user.id">
        	<div class="form-row">
				
				<div class="form-group col-md-3">
					<input type="text" class="form-control" v-model="user.name" :id="'name' + user.id" placeholder="Nom...">
				</div>

				<div class="form-group col-md-3">
					<input type="email" class="form-control" v-model="user.email" :id="'email' + user.id" placeholder="Email..." autocomplete="off">
				</div>

				<div class="form-group col-md-3">
					<input type="password" class="form-control" v-model="user.password" :id="'password' + user.id" placeholder="Laisser vide si inchangé...">
				</div>

				<div class="form-group col-md-2">
					<button class="btn btn-primary btn-block" @click="update(user)">
						Enregistrer
					</button>
				</div>

				<div class="form-group col-md-1">
					<button class="btn btn-danger btn-block" @click="destroy(user)">
						<i class="fa fa-trash"></i>
					</button>
				</div>

			</div>
        </div>

    </div>
</template>

<script>
    
    export default {
        
        data(){
        	return {
        		users: [],

        		create_item: {},
        		create_open: false,
        		create_status: null,
        		create_error: null,
        	}
        },

        mounted(){
        	this.resetCreateItem();
        	this.read();
        },

        methods: {

        	read(){

        		axios.get('/admin/user').then(r => {

        			this.users = r.data;

        		});
        	},

        	resetCreateItem(){
        		this.create_item = {
        			name: '',
        			email: '',
        			password: '',
        		}
        	},

        	openCreate(){
        		this.resetCreateItem();
        		this.create_open = true;
        		this.create_status = null;
        		this.create_error = null;
        	},

        	create(){

        		this.create_error = null;
        		this.create_status = 'pending';

        		axios.post('/admin/user/', this.create_item).then(r => {

        			this.create_open = false;
        			this.create_status = 'success';
                    this.read();

                }).catch(r => {

                	this.create_error = r.response.data;
                });
        	},

            update(user){

            	this.$set(user, 'update_status', 'pending');
            	this.$set(user, 'update_error', null);

            	var data = {
            		name: user.name,
            		email: user.email,
            		password: user.password,
            	};

                axios.put('/admin/user/' + user.id, data).then(r => {

                    this.$set(user, 'update_status', 'success');

                }).catch(r => {

                    this.$set(user, 'update_status', 'error');
                    this.$set(user, 'update_error', r.response.data);
                });

            },

            destroy(user){

            	axios.delete('/admin/user/' + user.id).then(r => {

                    this.read();

                }).catch(r => {

                    
                });

            },
        }
    }
</script>
