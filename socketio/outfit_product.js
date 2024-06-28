const express = require("express");
const app     = express();
const fs      = require("fs");

const options = {
    cert: fs.readFileSync("/home2/productsaaitd/ssl/certs/outfitnew_products_aait_d_com_b1190_90355_1721634238_488546f91361168936d620c3382651f3.crt"),
    key: fs.readFileSync("/home/productsaaitd/ssl/keys/b1190_90355_62f89726ee58bcfb274edbb77c492067.key"),
};

const server = require("https").createServer(options, app);
const io = require("socket.io")(server, {
    cors: {
        methods: ["GET", "PATCH", "POST", "PUT"],
        origin: true,
        credentials: true,
        transports: ["websocket", "polling"],
    },
});

const redis = require("redis");
var redisClient = redis.createClient();

io.on("connection", (socket) => {
    
    console.log("Client connected");

    redisClient.on("message", function (channel, message) {

        message = JSON.parse(message);

        console.log('message is '.message);

        console.log("message event is " + message.event);
        console.log("message data is " + message.data);

        socket.emit(message.event, message.data);
    });

    redisClient.subscribe("private-notification-outfit-product");

    socket.on("disconnect", function () {
        console.log("Client disconnected");
    });
});

const port = 3091;
const host = "outfitnew.products.aait-d.com";

server.listen(port, host, () => {
    console.log(`Server running at https://${host}:${port}`);
});
