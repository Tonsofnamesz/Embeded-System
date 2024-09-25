
import paho.mqtt.client as mqtt

class NetworkCommLib:
    def __init__(self, broker, port, node_id):
        self.broker = broker
        self.port = port
        self.node_id = node_id
        self.client = mqtt.Client(self.node_id)

    def connect(self):
        self.client.connect(self.broker, self.port)
        self.client.loop_start()

    def send_unicast(self, target_id, message):
        topic = f'unicast/{target_id}'
        self.client.publish(topic, message)

    def send_multicast(self, group, message):
        topic = f'multicast/{group}'
        self.client.publish(topic, message)

    def subscribe(self, topic, callback):
        self.client.subscribe(topic)
        self.client.on_message = callback

    def on_message(self, client, userdata, message):
        print(f"Received message: {message.payload.decode()}")

    def stop(self):
        self.client.loop_stop()
        self.client.disconnect()
