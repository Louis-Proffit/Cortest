if ("serial" in navigator) {
    console.log("Serial available")

    document.querySelector("#open").addEventListener('click', async (err) => {
        // Prompt user to select any serial port.
        const port = await navigator.serial.requestPort();

        port.addEventListener('connect', (event) => {
            console.log("Connected")
        });

        const {usbProductId, usbVendorId} = port.getInfo();

        console.log(usbProductId, usbVendorId)

        // parameters in https://web.dev/serial/
        await port.open({baudRate: 19200});

        document.querySelector("#close").addEventListener("click", async (err) => {
            await port.close().then(() => {
            }, (err) => {
                console.log(err)
            });
        })

        document.querySelector("#test").addEventListener("click", async () => {
            console.log("Testing connection");
            const writer = port.writable.getWriter();
            await writer.write(new Uint8Array([76, 13, 10]));
            writer.releaseLock();
        })

        document.querySelector("#send").addEventListener("click", async (err) => {
            console.log("Exchanging data");
            const writer = port.writable.getWriter();

            console.log("Fetching value to write");
            const encoder = new TextEncoder();
            const data = encoder.encode(document.querySelector("#input").value)
            console.log("Data to send : ", data);
            await writer.write(data);

            writer.releaseLock();

            console.log("Data sent, writer closed, opening reader")

            const reader = port.readable.getReader();

            let output = "";
            while (true) {
                const {value, done} = await reader.read();
                if (done) {

                    // Allow the serial port to be closed later.
                    reader.releaseLock();
                    break;
                }
                console.log("Appending ", value, " to result")
                output += value;

                if(value[value.length - 1] === 4) {
                    reader.releaseLock();
                    break;
                }
            }

            const decoder = new TextDecoder()
            console.log("Result : ", output)
            document.querySelector("#output").value = decoder.decode(output);
        })
    });

} else {
    console.log("Serial unavailable")
}