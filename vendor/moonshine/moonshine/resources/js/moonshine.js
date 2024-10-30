import {moonShineRequest} from './alpine/asyncFunctions.js'
import {Iterable} from './composable/iterable.js'
import {UI} from './composable/ui.js'

export class MoonShine {
  constructor() {
    this.callbacks = {}
    this.iterable = new Iterable()
    this.ui = new UI()
  }

  onCallback(name, callback) {
    if (typeof callback === 'function') {
      this.callbacks[name] = callback
    }
  }

  request(t, url, method = 'get', body = {}, headers = {}, data = {}) {
    if (!(data instanceof ComponentRequestData)) {
      data = new ComponentRequestData().fromObject(data)
    }

    moonShineRequest(t, url, method, body, headers, data)
  }
}

export class ComponentRequestData {
  constructor() {
    this._events = ''
    this._selector = ''

    this._beforeFunction = null
    this._responseFunction = null

    this._beforeCallback = null
    this._afterCallback = null

    this._errorCallback = null
    this._afterErrorCallback = null

    this._extraAttributes = {}
  }

  get events() {
    return this._events
  }

  withEvents(value) {
    this._events = value

    return this
  }

  get selector() {
    return this._selector
  }

  withSelector(value) {
    this._selector = value

    return this
  }

  get beforeFunction() {
    return this._beforeFunction
  }

  hasBeforeFunction() {
    return this._beforeFunction !== null && this._beforeFunction
  }

  withBeforeFunction(value) {
    this._beforeFunction = value

    return this
  }

  get beforeCallback() {
    return this._beforeCallback
  }

  hasBeforeCallback() {
    return this._beforeCallback !== null && typeof this._beforeCallback === 'function'
  }

  withBeforeCallback(value) {
    this._beforeCallback = value

    return this
  }

  get responseFunction() {
    return this._responseFunction
  }

  hasResponseFunction() {
    return this._responseFunction !== null && this._responseFunction
  }

  withResponseFunction(value) {
    this._responseFunction = value

    return this
  }

  get afterCallback() {
    return this._afterCallback
  }

  hasAfterCallback() {
    return this._afterCallback !== null && typeof this._afterCallback === 'function'
  }

  withAfterCallback(value) {
    this._afterCallback = value

    return this
  }

  get errorCallback() {
    return this._errorCallback
  }

  hasErrorCallback() {
    return this._errorCallback !== null && typeof this._errorCallback === 'function'
  }

  withErrorCallback(value) {
    this._errorCallback = value

    return this
  }

  get afterErrorCallback() {
    return this._afterErrorCallback
  }

  hasAfterErrorCallback() {
    return this._afterErrorCallback !== null && typeof this._afterErrorCallback === 'function'
  }

  withAfterErrorCallback(value) {
    this._afterErrorCallback = value

    return this
  }

  get extraAttributes() {
    return this._extraAttributes
  }

  withExtraAttributes(value) {
    this._extraAttributes = value

    return this
  }

  fromDataset(dataset = {}) {
    return this.withEvents(dataset.asyncEvents ?? '')
      .withSelector(dataset.asyncSelector ?? '')
      .withResponseFunction(dataset.asyncCallback ?? null)
      .withBeforeFunction(dataset.asyncBeforeFunction ?? null)
  }

  fromObject(object = {}) {
    return this.withEvents(object.events ?? '')
      .withSelector(object.selector ?? '')
      .withResponseFunction(object.responseFunction ?? null)
      .withBeforeFunction(object.beforeFunction ?? null)
      .withBeforeCallback(object.beforeCallback ?? null)
      .withAfterCallback(object.afterCallback ?? null)
      .withErrorCallback(object.errorCallback ?? null)
      .withAfterErrorCallback(object.afterErrorCallback ?? null)
      .withExtraAttributes(object.extraAttributes ?? null)
  }
}
