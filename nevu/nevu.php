<?php
if ($_GET['nombre'] != null) {
    ?>
    <!doctype html> 
    <html lang="en"> 
        <head> 
            <meta charset="UTF-8" />
            <title>Nevu</title>
            <script type="text/javascript" src="js/phaser.min.js"></script>
            <style type="text/css">
                body {
                    margin: 0;
                    background-image: url("assets/fondo.jpg");
                }
                #game{
                    width:805px;
                    height:405px;
                    margin: 4px;
                    border: 2px solid #ccc;
                    border-radius: 10px;
                }
                #chat{
                    text-align: left;
                    background-color: #fff;
                    width:800px;
                    height:150px;
                    border: 2px solid #ccc;
                    margin: 2px;
                    overflow-x:hidden;
                    overflow-y:scroll;
                    padding: 5px;    
                    border-radius: 10px;
                }
            </style>

            <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
            <link href="style/bootstrap.min.css" rel="stylesheet">
            <script src="js/bootstrap.min.js"></script>
        </head>
        <body >
            <br>
        <center>
            <div id="game">


            </div>
            <div id="chat">
                <span style="color: #204d74;"><b>CHAT ONLINE</b></span>
                <hr>
                <ul style="float: left;list-style-type: none;" id="mensajes">

                </ul>
            </div>
            <div style="background-color: #fff;width:800px;height:50px;border: 2px solid #ccc;padding: 5px;" id="chat">

                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-8"><input id="mensaje" class="form-control" type="text"></div>
                    <div class="col-xs-6 col-md-4"><button  class="btn btn-success" onclick="enviarMensaje()" value="Enviar mensaje">Enviar mensaje</button>
                        <button type="button" class="btn btn-danger btn-mini">
                            <span onclick="sonido();"  aria-hidden="true">Sound</span> 
                        </button>
                    </div>
                </div>
            </div>

        </center>
        <script src="js/socket.io.js"></script>
        <script type="text/javascript">
                                $('input[type=text]').on('keyup', function (e) {
                                    if (e.which == 13) {
                                        enviarMensaje();
                                    }
                                });

                                function sonido() {
                                    levelAudio.stop();
                                }
                                RemotePlayer = function (tipo, nombre, index, game, player, startX, startY) {

                                    var x = startX;
                                    var y = startY;
                                    //alert(tipo);

                                    this.game = game;
                                    this.health = 3;
                                    this.player = player;
                                    this.alive = true;
                                    this.player.name = nombre;
                                    this.player.tipo = tipo;
                                    this.player = game.add.sprite(x, y, 'dude', parseInt(tipo));
                                    //this.player.animations.add('move', [0, 1, 2, 3, 4, 5, 6, 7], 20, true);
                                    //this.player.animations.add('stop', [3], 20, true);
                                    this.game.physics.enable(this.player, Phaser.Physics.ARCADE);
                                    this.player.anchor.setTo(0.5, 0.5);
                                    this.player.scale.x = 0.4;
                                    this.player.scale.y = 0.4;
                                    this.player.name = index.toString();
                                    this.player.body.immovable = false;
                                    this.player.body.collideWorldBounds = true;
                                    this.player.angle = 0;
                                    this.lastPosition = {x: x, y: y}
                                };
                                RemotePlayer.prototype.update = function () {
                                    if (this.player.x != this.lastPosition.x || this.player.y != this.lastPosition.y) {
                                        this.player.play('move');
                                        this.player.rotation = 0;
                                        // this.player.rotation = this.game.physics.arcade.angleBetween(this.tank, this.player);
                                        //this.player.rotation = Math.PI + game.physics.angleToXY(this.player, this.lastPosition.x, this.lastPosition.y);
                                        //this.game.physics.arcade.velocityFromRotation(this.player.rotation, 100, this.player.body.velocity);
                                    } else {
                                        this.player.play('stop');
                                    }

                                    this.lastPosition.x = this.player.x;
                                    this.lastPosition.y = this.player.y;
                                };
                                var game = new Phaser.Game(800, 400, Phaser.AUTO, 'game', {preload: preload, create: create, update: update, render: render});
                                function preload() {
                                    game.load.image('blanco', 'assets/light_grass.png');
                                    game.load.image('puerta', 'assets/puerta.png');
                                    game.load.spritesheet('dude', 'assets/sprites.png', 152, 195);
                                    game.load.audio('sound', 'assets/sound.mp3');
                                    game.load.audio('tip', 'assets/tip.mp3');
                                    levelAudio = game.add.audio('sound');
                                    levelAudio.play('', 0, 0.3, true);
                                    //game.load.spritesheet('enemy', 'assets/dude.png', 64, 64);
                                }

                                var socket; // Socket connection
                                var land;
                                var player;
                                var remote_players;
                                var currentSpeed = 0;
                                var cursors;
                                var levelAudio;
                                var tip;
                                var puerta;
                                function create() {



                                    tip = game.add.audio('tip');
                                    socket = io.connect("http://autounido.cl/", {port: 8130, transports: ["websocket"]});
                                    //  Resize our game world to be a 2000 x 2000 square
                                    //game.world.setBounds(-500, -500, 800, 400);
                                    //game.physics.arcade.gravity.y = 0;
                                    //  Our tiled scrolling background
                                    land = game.add.tileSprite(0, 0, 800, 450, 'blanco');
                                    land.fixedToCamera = true;
                                    //objetos 
                                    puerta = game.add.sprite(game.world.centerX, game.world.centerY, 'puerta');
                                    puerta.name = 'puerta';
                                    game.physics.enable(puerta, Phaser.Physics.ARCADE);
                                    //puerta.body.velocity.setTo(200, 200);
                                    puerta.body.collideWorldBounds = true;
                                    puerta.body.immovable = true;
                                    //puerta.body.bounce.set(0.8);
                                   

                                    //puerta.body.gravity.set(0, 180);
                                    //  The base of our player
                                    var startX = Math.round(Math.random() * (800) - 450),
                                            startY = Math.round(Math.random() * (800) - 450);
                                    player = game.add.sprite(startX, startY, 'dude', <?= $_GET['tipo'] ?>);
                                    player.anchor.setTo(0.5, 0.5);
                                    //player.animations.add('move', [0, 1, 2, 3, 4, 5, 6, 7], 20, true);
                                    //player.animations.add('stop', [3], 20, true);
                                    // sprite.scale.x
                                    player.scale.x = 0.4;
                                    player.scale.y = 0.4;
                                    //  This will force it to decelerate and limit its speed
                                    game.physics.enable(player, Phaser.Physics.ARCADE);
                                    player.body.drag.set(0.2);
                                    player.body.maxVelocity.setTo(400, 400);
                                    //player.body.collideWorldBounds = true;
                                    // player.body.collideWorldBounds = true;
                                    //  Create some baddies to waste :)
                                    player.body.collideWorldBounds = true;
                                    player.nombre = "<?= $_GET['nombre'] ?>";
                                    player.tipo = "<?= $_GET['tipo'] ?>";
                                    remote_players = [];
                                    player.bringToTop();
                                    game.camera.follow(player);
                                    game.camera.deadzone = new Phaser.Rectangle(150, 150, 500, 300);
                                    game.camera.focusOnXY(0, 0);
                                    cursors = game.input.keyboard.createCursorKeys();
                                    // Start listening for events
                                    setEventHandlers();
                                }

                                var setEventHandlers = function () {
                                    // Socket connection successful
                                    socket.on("connect", onSocketConnected);
                                    // Socket disconnection
                                    socket.on("disconnect", onSocketDisconnect);
                                    // New player message received
                                    socket.on("new player", onNewPlayer);
                                    // Player move message received
                                    socket.on("move player", onMovePlayer);
                                    // Player removed message received
                                    socket.on("remove player", onRemovePlayer);
                                    
                                    socket.on("chat message", onChatMessage);
                                };
                                // Socket connected
                                function onSocketConnected() {
                                    console.log("Connected to socket server");
                                    // Send local player data to the game server
                                    socket.emit("new player", {tipo: player.tipo, nombre: player.nombre, x: player.x, y: player.y});
                                }
                                ;
                                // Socket disconnected
                                function onSocketDisconnect() {
                                    console.log("Disconnected from socket server");
                                }
                                ;
                                // New player
                                function onNewPlayer(data) {
                                    console.log("New player connected: " + data.id + " " + data.nombre);
                                    // Add new player to the remote players array
                                    remote_players.push(new RemotePlayer(data.tipo, data.nombre, data.id, game, player, data.x, data.y));
                                }
                                ;
                                // Move player
                                function onMovePlayer(data) {

                                    var movePlayer = playerById(data.id);
                                    // Player not found
                                    if (!movePlayer) {
                                        console.log("Player not found: " + data.id);
                                        return;
                                    }
                                    ;
                                    // Update player position
                                    movePlayer.player.x = data.x;
                                    movePlayer.player.y = data.y;
                                }
                                ;
                                // Remove player
                                function onRemovePlayer(data) {

                                    var removePlayer = playerById(data.id);
                                    // Player not found
                                    if (!removePlayer) {
                                        console.log("Player not found: " + data.id);
                                        return;
                                    }
                                    ;
                                    removePlayer.player.kill();
                                    // Remove player from array
                                    remote_players.splice(remote_players.indexOf(removePlayer), 1);
                                }
                                ;
                                function update() {
                                    //puerta.body.gravity.y = 0;
                                    for (var i = 0; i < remote_players.length; i++)
                                    {
                                        if (remote_players[i].alive)
                                        {
                                            remote_players[i].update();
                                            //game.physics.arcade.collide(player, remote_players[i].player);
                                            //game.physics.arcade.collide(puerta, player);
                                        }
                                    }
                                    //movimiento
                                    //movimiento
                                    if (game.input.keyboard.isDown(Phaser.Keyboard.LEFT))
                                    {
                                        player.x -= 4;
                                    }
                                    else if (game.input.keyboard.isDown(Phaser.Keyboard.RIGHT))
                                    {
                                        player.x += 4;
                                    }

                                    if (game.input.keyboard.isDown(Phaser.Keyboard.UP))
                                    {
                                        player.y -= 4;
                                    }
                                    else if (game.input.keyboard.isDown(Phaser.Keyboard.DOWN))
                                    {
                                        player.y += 4;
                                    }


                                    if (game.input.mousePointer.isDown)
                                    {
                                        //  400 is the speed it will move towards the mouse
                                        game.physics.arcade.moveToPointer(player, 400);

                                        //  if it's overlapping the mouse, don't move any more
                                        if (Phaser.Rectangle.contains(player.body, game.input.x, game.input.y))
                                        {
                                            player.body.velocity.setTo(0, 0);
                                        }
                                    }
                                    else
                                    {
                                        player.body.velocity.setTo(0, 0);
                                    }

                                    land.tilePosition.x = -game.camera.x;
                                    land.tilePosition.y = -game.camera.y;
    //                        if (game.input.activePointer.isDown)
    //                        {
    //                            if (game.physics.distanceToPointer(player) >= 10) {
    //                                currentSpeed = 300;
    //
    //                                player.rotation = game.physics.angleToPointer(player);
    //                            }
    //                        }
                                    game.physics.arcade.collide(puerta, player);
                                    //  game.physics.arcade.collide(player, players);
                                    socket.emit("move player", {x: player.x, y: player.y});
                                }

                                function render() {

                                }

                                // Find player by ID
                                function playerById(id) {
                                    var i;
                                    for (i = 0; i < remote_players.length; i++) {
                                        if (remote_players[i].player.name == id)
                                            return remote_players[i];
                                    }
                                    ;
                                    return false;
                                }
                                ;
                                function enviarMensaje() {
                                    tip.play('', 0, 0.3, false);
                                    $('#mensajes').prepend($('<li>').html("<b>Yo</b> : " + $('#mensaje').val()));
                                    socket.emit('chat message', $('#mensaje').val(), player.nombre);
                                    $('#mensaje').val('');

                                    return false;
                                }
                                ;
                                function onChatMessage(nombre, message) {
                                    //alert(message);

                                    tip.play('', 0, 0.3, false);
                                    $('#mensajes').prepend($('<li>').html("<b>" + nombre + "</b> : " + message));
                                }
                                ;




        </script>

    </body>
    </html>
    <?php
} else {
    header("Location:index.php");
}
?>