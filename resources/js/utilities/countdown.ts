const countdown = {
  calculate(endTime: number) {
    const now = new Date().getTime();
    const remainder = endTime - now;
    const days = Math.floor(remainder / (1000 * 60 * 60 * 24));
    const hours = Math.floor(
      (remainder % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
    );
    const minutes = Math.floor((remainder % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((remainder % (1000 * 60)) / 1000);

    return {
      remainder,
      days,
      hours,
      minutes,
      seconds,
    };
  },
};
export default countdown;
