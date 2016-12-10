<template>
    <div class="container">
        <div class="grid">
            {{ grid }}
            <div class="row" v-for="row in grid">
                <div class="cell" v-for="cell in row">{{ cell }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function(){
            return {
                grid : []
            }
        },
        mounted() {
            console.log( 'Component ready.' );
            console.log("grid gr ->",this.grid);

            // create the empty grid;
            for(var i = 0; i < 3; i++){
                this.grid[i] = [];
                for(var j = 0; j < 3; j++) {
                    this.grid[i][j] = null;
                }
            }

            console.log("grid gr ->",this.grid);

            this.$http.get('/play').then(function(resp){
                console.log("resp ->",resp);
            }, function(){

            });

            // get game state (waiting for player or ready to play)

        }
    }
</script>

