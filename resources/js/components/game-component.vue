<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body" v-if="loaded">
                        <div class="row">
                            <div class="col-md-6">
                                <h2>You:</h2>
                                {{ player.name || 'N/A' }}
                            </div>
                            <div class="col-md-6 text-right" v-if="competitor">
                                <h2>Competitor:</h2>
                                {{ competitor.name || 'N/A' }}
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <h3 class="col-md-6">Started at: {{ game.startedAt || 'N/A' }}</h3>
                            <h3 class="col-md-6 text-right">Ended at: {{ game.endedAt || 'N/A' }}</h3>
                        </div>
                        <div v-if="game.winner">
                            <br>
                            <h3 class="text-center">
                                Winner: {{ game.winner.name }}
                            </h3>
                        </div>

                        <div v-if="!player.name">
                            <br>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Name:</label>
                                        <input class="form-control" v-model="playerName"/>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-success" @click="saveName">
                                            Save name
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="player.name && (!competitor || !competitor.name)">
                            <br>
                            <h2 class="text-center">Waiting for competitor...</h2>
                        </div>

                        <div v-if="player.name && competitor && competitor.name">
                            <br>
                            <div class="row" v-for="x in 3">
                                <div class="col-md-4" v-for="y in 3" style="margin-bottom: 25px">
                                    <button class="btn btn-block btn-primary" style="height: 273px; font-size: 100px"
                                            v-if="hasMark(x - 1, y -1)" :disabled="game.winner">
                                        {{ getMarkLabel(x - 1, y - 1) }}
                                    </button>
                                    <button class="btn btn-block btn-info" style="height: 273px; font-size: 100px"
                                            v-else @click="makeAMove(x - 1, y - 1)" :disabled="game.winner">
                                    </button>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" v-else>
                        <h1 class="text-center">
                            Loading...
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            id: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                game: {
                    owner: null,
                    competitor: null,
                    winner: null,
                    startedAt: null,
                    endedAt: null,
                    stepsCount: null
                },
                player: {
                    name: null,
                    steps: []
                },
                competitor: {
                    name: null,
                    steps: []
                },
                playerName: null,
                loaded: false
            };
        },
        methods: {
            saveName() {
                axios.post(
                    `/api/game/${this.id}/set-name`,
                    {name: this.playerName}
                ).catch(console.log)
            },
            hasMark(x, y) {
                const playerStepIndex = this.player.steps.findIndex(step => step.x == x && step.y == y);
                if (playerStepIndex >= 0) {
                    return true;
                }
                return this.competitor.steps.findIndex(step => step.x == x && step.y == y) >= 0;
            },
            getMarkLabel(x, y) {
                if (this.player.steps.findIndex(step => step.x == x && step.y == y) >= 0) {
                    return 'X';
                }
                return '0';
            },
            makeAMove(x, y) {
                axios.post(
                    `/api/game/${this.id}/move`,
                    {x, y}
                ).catch(console.log)
            }
        },
        created() {
            setInterval(() => axios.get(`/api/game/${this.id}`)
                    .then(response => {
                        this.game = response.data.game;
                        this.player = response.data.player;
                        this.competitor = response.data.competitor;
                        this.loaded = true;
                    })
                    .catch(console.log)
                , 1000)
        }
    }
</script>
