/* 
 * Gestion de la connection avec le lecteur optique
 */


export class CortestPort {

    /**
     *
     * @returns {Promise<void>}
     */
    async connect() {
        throw Error("Abstract method")
    }

    /**
     *
     * @returns {Promise<boolean>}
     */
    async tryConnect() {
        throw Error("Abstract method")
    }

    /**
     *
     * @returns {Promise<string>}
     */
    async read() {
        throw Error("Abstract method")
    }

    /**
     *
     * @param content
     * @returns {Promise<void>}
     */
    async write(content) {
        throw Error("Abstract method")
    }

    /**
     *
     * @param content
     * @returns {Promise<string>}
     */
    async exchange(content) {
        throw Error("Abstract method")
    }
}


const BAUD_RATE = 19200
const TRY_CONNECT_COMMAND = "V"
const TRY_CONNECT_OUTPUT_REGEX = /\x01\x02.*r\n\x03\x04/
const CHAR_END_OF_TRANSMISSION = '\x04'
const CHAR_END_OF_TEXT = '\x03'

export class CortestSerialPort extends CortestPort {

    constructor(baudRate) {
        super();
        this.baudRate = baudRate
        this.port = null
    }

    async connect() {
        console.log("Connecting to serial port, with baudRate : ", this.baudRate)
        this.port = await navigator.serial.requestPort();
        await this.port.open({baudRate: this.baudRate});
        await this.read();
    }

    async tryConnect() {
        const output = await this.exchange(TRY_CONNECT_COMMAND);

        return output.match(TRY_CONNECT_OUTPUT_REGEX) !== null;
    }

    async read() {
        if (this.port == null) {
            throw Error("Port doesn't exist yet, connect failed or not called")
        }

        const reader = this.port.readable.getReader();
        let buffer = ""

        while (true) {
            const {value, done} = await reader.read();
            if (done) {
                reader.releaseLock();
                break;
            }
            buffer += new TextDecoder().decode(value);
        }

        return buffer
    }

    async write(content) {
        console.log('Requête : ' + content);

        // envoi de la requête
        const writer = this.port.writable.getWriter();
        await writer.write(new TextEncoder().encode(content));

        writer.releaseLock();
    }

    async exchange(content) {
        await this.write(content)

        let buffer = ""

        for (let n = 0; n < 300; n++) {
            await timeout(10);
            if (buffer.slice(-1) === CHAR_END_OF_TRANSMISSION || buffer.slice(-1) === CHAR_END_OF_TEXT) {

                const cache = Object.assign({}, {text: buffer}).text;
                await timeout(40);

                if (buffer === cache) {
                    console.log('Response : ');
                    console.log({buffer: buffer});

                    return buffer;
                }
            }
        }

        return buffer;
    }
}

export class CortestPromptPort extends CortestPort {

    constructor() {
        super();
    }

    async tryConnect() {
        console.log("Trying connection to prompt port")
        return true
    }

    async connect() {
        console.log("Connecting prompt port")
    }

    async write(content) {
        console.log("Wrote ", content, " to prompt port")
    }

    async exchange(content) {
        console.log("Command ", content, " to prompt port");
        let prompt = window.prompt("Résultat du " + content, "");
        prompt = prompt.replaceAll('\\x15', String.fromCharCode(21));
        prompt = prompt.replaceAll('\\r', String.fromCharCode(13));
        prompt = prompt.replaceAll('\\n', String.fromCharCode(10));
        prompt = prompt.replaceAll('\\x03', String.fromCharCode(3));
        prompt = prompt.replaceAll('\\x04', String.fromCharCode(4));
        prompt = prompt.replaceAll('\\x02', String.fromCharCode(2));
        prompt = prompt.replaceAll('\\x01', String.fromCharCode(1));
        console.log('get = ' + prompt);
        return prompt;
    }
}

export function timeout(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

export function cortestSerialPort() {
    return new CortestSerialPort(BAUD_RATE)
}

export function cortestPromptPort() {
    return new CortestPromptPort()
}