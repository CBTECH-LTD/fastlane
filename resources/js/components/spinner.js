export function Spinner () {
    return {
        active: false,
        setStatus (status) {
            this.active = status
        }
    }
}
