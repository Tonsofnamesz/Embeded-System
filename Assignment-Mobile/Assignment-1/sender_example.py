from network_comm_lib import NetworkCommLib

def main():
    node = NetworkCommLib(broker="localhost", port=1883, node_id="node1")
    node.connect()

    node.send_unicast(target_id="node2", message="Hello, node2!")

    node.send_multicast(group="group1", message="Hello, group1!")

if __name__ == "__main__":
    main()
