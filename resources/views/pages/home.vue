<template>
    <q-layout view="hHh lpR fFf">

        <q-header elevated class="bg-primary text-white" height-hint="98">
            <q-toolbar>
                <q-toolbar-title>
                    Тендерное сопровождение
                </q-toolbar-title>
                <q-btn flat label="Выберите город" icon="location_on" @click="dialog_select_place.status = true" />
            </q-toolbar>
        </q-header>

        <q-page-container class="bg-white">
            <q-page>
                <br><br>
                Изначально хотел сделать переход на поддомен без перезагрузки, но так нельзя (протокол безопасности браузера).
                <br>
                Какие существуют теги СЕО не знаю (с ними полноценно не работал), при необходимости будут изучены. <br>
                <br><br>
                О проекте: <br>
                Нажмите на "Выберите город" для выбора города. <br>
                Начните вводить в поиске название (или слог) для получения результата. Поиск работает от 3 символов, иначе покажется подсказка через 1 секунду.
                <br>
                Нажмите на выбранный город для перехода. <br>
                <br><br>
                PS: Хотелось получше сделать тестовое. Все таки это моя работа) <br>
            </q-page>
        </q-page-container>
        <q-dialog v-model="dialog_select_place.status" >
            <q-card style="min-width: 50vw;">
                <q-card-section class="q-py-none">
                    <q-input v-model="dialog_select_place.search" borderless label="Поиск..">
                        <q-tooltip v-if="dialog_select_place.search.length < 3" :delay="1000" anchor="center right" self="center left" :offset="[10, 10]">
                            Введите 3 символа
                        </q-tooltip>
                    </q-input>
                </q-card-section>
                <hr>
                <q-card-section class="row">
                    <div class="col-12 col-md-6 col-lg-4 select-place__item" v-for="el in dialog_select_place.result" @click="event__change_place(el)">
                        <q-icon name="chevron_right" class="select-place__item__icon"/> {{ el.name }}
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>
    </q-layout>
</template>

<script>
import { ref } from 'vue'

export default {
    props: ['select_place', 'area'],
    data(){
        return {
            dialog_select_place: {
                status: false,
                search: '',
                result: this.area
            },
            options: [

            ],
        }
    },
    watch: {
        'dialog_select_place.search': function (text){
            console.log(text)
            if (text.length >= 3){
                this.axios.post('/search-place', {
                    q: text
                }).then((response)=>{
                    this.dialog_select_place.result = response.data
                })
            }

        }
    },
    methods:{
        event__change_place(el){
            window.location.href = "/change-place/" + el.type + '/' + el.slug
        }
    }
}
</script>
<style lang="css">
.select-place__item{
    cursor: pointer;
}
.select-place__item__icon{
    opacity: 0;
    transition: opacity 0.2s;
}
.select-place__item:hover .select-place__item__icon{
    opacity: 1;
}
</style>
