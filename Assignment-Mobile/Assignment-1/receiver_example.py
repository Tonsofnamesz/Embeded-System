from network_comm_lib import NetworkCommLib

def on_message(client, userdata, message):
    print(f"Received message: {message.payload.decode()}")

def main():
    node = NetworkCommLib(broker="localhost", port=1883, node_id="node2")
    node.connect()

    node.subscribe(f'unicast/node2', on_message)

    node.subscribe(f'multicast/group1', on_message)

if __name__ == "__main__":
    main()
