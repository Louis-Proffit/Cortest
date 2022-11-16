if ("serial" in navigator) {
    console.log("Serial available")

    document.querySelector("#open").addEventListener('click', async () => {
        // Prompt user to select any serial port.
        const port = await navigator.serial.requestPort();

        const {usbProductId, usbVendorId} = port.getInfo();

        console.log(usbProductId, usbVendorId)

        // parameters in https://web.dev/serial/
        await port.open({baudRate: 9600});

        const reader = port.readable.getReader();

        document.querySelector("#close").addEventListener("click", async () => {
            await port.close();
        })

        document.querySelector("#send").addEventListener("click", async () => {
            const writer = port.writable.getWriter();

            const data = [...document.querySelector("#input").value];
            await writer.write(data);

            writer.releaseLock();

            let output = "";
            while (true) {
                const {value, done} = await reader.read();
                if (done) {
                    // Allow the serial port to be closed later.
                    reader.releaseLock();
                    break;
                }
                output += value;
            }

            document.querySelector("#output").value = output;
        })
    });

} else {
    console.log("Serial unavailable")
}