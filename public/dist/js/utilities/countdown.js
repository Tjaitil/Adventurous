const countdown = {
    calculate(endTime) {
        let now = new Date().getTime();
        let remainder = endTime - now;
        let days = Math.floor(remainder / (1000 * 60 * 60 * 24));
        let hours = Math.floor((remainder % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((remainder % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((remainder % (1000 * 60)) / 1000);
        return {
            remainder,
            days,
            hours,
            minutes,
            seconds,
        };
    }
};
export default countdown;
