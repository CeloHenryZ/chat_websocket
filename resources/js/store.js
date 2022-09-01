import {createApp} from "vue";
import { createStore } from 'vuex'
import createPersistedState from "vuex-persistedstate";


export const store = createStore({

    state: {
        user: {}
    },
    mutations: {
        setUserState: (state, value) => { state.user = value}
    },
    actions: {
        userStateAction: () => {
            axios.get('api/user/myUser').then(response => {
                const userLoggedResponse = response.data.myUser

                store.commit('setUserState', userLoggedResponse )
            })
        }
    },
    plugins:  [ createPersistedState() ]
})
