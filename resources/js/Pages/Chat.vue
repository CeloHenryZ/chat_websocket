<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex" style="min-height: 400px; max-height: 400px; ">

                    <!-- list users -->
                    <div
                        class="bg-gray-200 bg-opacity-25 border-r border-gray-200 overflow-y-scroll"
                        :class="(userActive == null) ? 'w-full' : 'w-3/12' ">
                        <ul>
                            <li
                                v-for="user in users" :key="user.id"
                                @click="() => {loadMessages(user.id)}"
                                :class="(userActive != null && userActive.id == user.id) ? 'bg-gray-200 bg-opacity-50' : '' "
                                class="p-6 text-lg text-gray-600 leading-7 font-semibold border-b border-gray-200 hover:bg-gray-200 hover:cursor-pointer hover:bg-opacity-50" >
                                <p class="flex items-center">
                                    {{ user.name }}
                                    <span v-if="user.notification" class="ml-2 w-2 h-2 bg-blue-400 rounded-full"></span>
                                </p>
                            </li>
                        </ul>
                    </div>

                    <!-- box message -->
                    <div v-if="userActive !== null" class="w-9/12 flex flex-col justify-between overflow-y-scroll">

                        <!-- Message -->
                        <div v-if="userActive !== null" class="w-full p-6 flex flex-col">
                            <div
                                v-for="message in messages" :key="message.id"
                                :class="(message.from == $page.props.user.id) ? 'text-right' : ''"
                                class="message w-full mb-3">
                                <p
                                    :class="(message.from == $page.props.user.id) ? 'messageFromMe' : 'messageToMe'"
                                    class="inline-block p-2 rounded-md messageFromMe" style="max-width: 75%;">
                                    {{message.content}}
                                </p>
                                <span class="block mt-1 text-xs text-gray-500">{{moment(message.created_at ).format('DD/MM/YYYY HH:mm')}}</span>
                            </div>
                        </div>

                        <!-- Form -->
                        <div v-if="userActive != null" class="w-full bg-gray-200 bg-opacity-25 p-6 border-t border-gray-200">
                            <form v-on:submit.prevent="sendMessage">
                                <div class="flex rounded-md overflow-hidden border border-gray-300">
                                    <input v-model="message" type="text" class="flex-1 px-4 py-2 text-sm focus:outline-none">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py2">
                                        Enviar
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import moment from 'moment';
    import { store } from '../store';
    import createPersistedState from "vuex-persistedstate";

    export default {
        components: {
            AppLayout,
        },
        data() {
            return {
                users: [],
                messages: [],
                userActive: null,
                message: ''
            }
        },
        computed: {
           myUser() {
               return store.state.user
           }
        },
        methods: {
            scrollToBottom: function() {
                if(this.messages.length ){
                    document.querySelectorAll('.message:last-child')[0].scrollIntoView()
                }

            },

            loadMessages: async function(userId) {
                axios.get(`api/user/${userId}`).then(response => {
                    this.userActive = response.data.user
                })

                await axios.get(`api/messages/${userId}`).then(response => {
                    this.messages = response.data.messages
                })

                this.scrollToBottom()
            },

            sendMessage: function() {
                axios.post('api/message/sendMessage', {
                    'contentMessage': this.message,
                    'to': this.userActive.id
                }).then( response => {
                    this.messages.push({
                        'from': this.myUser.id,
                        'to': this.userActive.id,
                        'content': this.message,
                        'created_at': new Date().toISOString(),
                        'updated_at': new Date().toISOString(),
                    })

                    this.message = ''
                })
                this.scrollToBottom()
            },

            //pegar data e hora da msg
            moment(arg) {
                return moment(arg)
            },
        },

        mounted() {
            axios.get('api/users').then(response => {
                this.users = response.data.users
            })

            Echo.private(`user.${this.myUser.id}`).listen('.sendMessage', async (event) => {
                if(this.userActive && this.userActive.id === event.message.from) {
                   await this.messages.push(event.message)
                    this.scrollToBottom()
                }else {
                    const user = this.users.filter((user) => {
                        if(user.id == event.message.from){
                            return user
                        }
                    })

                    if(user) {
                        this.$set(user[0], 'notification', true)
                    }
                }

                console.log(event)
            })
        },
    }
</script>

<style>
.messageFromMe{
    @apply bg-indigo-300 bg-opacity-25
}

.messageToMe{
    @apply bg-gray-300 bg-opacity-25
}
</style>
