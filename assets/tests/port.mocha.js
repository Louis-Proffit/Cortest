const assert = require("assert")
import {cortestPromptPort, cortestSerialPort} from "../port.js"
describe('Port', function () {
    describe('connect', function () {
        it('Prompt port should connect', function () {
            const port = cortestPromptPort()
            assert.ok(port.connect())
        });
        it('Serial port should connect', function() {
            const port = cortestSerialPort()
            assert.ok(port.connect())
        })
    });
});
