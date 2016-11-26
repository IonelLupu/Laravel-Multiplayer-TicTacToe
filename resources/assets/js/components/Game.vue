<template>
    <div class="game">

        <hr>

        <!--<span class="animated flip text-center your-turn">Your Turn!!</span>-->

        <div class="board" v-if="isStarted">
            <div class="row">
                <div class="cell"></div>
                <div class="cell"></div>
                <div class="cell"></div>
            </div>
            <div class="row">
                <div class="cell"></div>
                <div class="cell"></div>
                <div class="cell"></div>
            </div>
            <div class="row">
                <div class="cell"></div>
                <div class="cell"></div>
                <div class="cell"></div>
            </div>
        </div>

        <h2 class="animated bounceIn text-center" v-else>Waiting for player!!!</h2>

        <hr>

    </div>
</template>

<script>
    export default {
        data () {
            return {
              isStarted: false
            }
        },

        created(){
            // check server
            this.$http.get('/api/play').then((response) => {
                // success callback
                console.log("success",response)
            }, (response) => {
                // error callback
                console.log("error",response)
            });
        }
    }


</script>


<style>
    .game{
        overflow: hidden;
    }
    h1,h2,h3,h4,h5,h6{
        color: lightgrey;
    }
    .board {
        text-align: center;
        position: relative;
    }

    .cell {
        width: 100px;
        height: 100px;
        background-color: lightgrey;
        display: inline-block;
    }

    .game-container {
        background-color: #5c5d5d;
    }

    .your-turn{

        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);

        padding: 5px 10px;
        z-index: 99999;
        color: #00d700;
        font-size: 50px;
        font-weight: bolder;
        background-color: rgba(0,0,0,0.5);
    }







</style>
