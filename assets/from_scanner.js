import {Grid} from "ag-grid-community"
import {GrilleManager} from "./grille_manager"
import $ from "jquery"
import {
    tellFatalError,
    askFID,
    askAlready,
    askQCM,
    askCodeBarre,
    askManualLink,
    formDate,
    formInput,
    formSelect,
    formConfirm,
    makeHTMLQCM,
    makeHTMLLink
} from "./intervention_manuelle"

import {cortestPromptPort, cortestSerialPort, timeout} from "./port"

global.$ = $
global.Grid = Grid
global.GrilleManager = GrilleManager
global.tellFatalError = tellFatalError
global.askFID = askFID
global.askAlready = askAlready
global.askQCM = askQCM
global.askCodeBarre = askCodeBarre
global.askManualLink = askManualLink
global.formDate = formDate
global.formInput = formInput
global.formSelect = formSelect
global.formConfirm = formConfirm
global.makeHTMLQCM = makeHTMLQCM
global.makeHTMLLink = makeHTMLLink
global.cortestSerialPort = cortestSerialPort
global.cortestPromptPort = cortestPromptPort
global.timeout = timeout
