import { state } from "./state.js";
import { formatDate } from "./utils.js";

export function renderWorkoutsList() {
  const ul = document.getElementById("workouts-list");
  ul.innerHTML = ""; // clear

  state.workouts.forEach((wk) => {
    const li = document.createElement("li");
    li.textContent = `${wk.name} (created: ${formatDate(wk.created_at)})`;

    const btn = document.createElement("button");
    btn.textContent = "Open";
    btn.addEventListener("click", () => {
      // custom event or callback
      const evt = new CustomEvent("openWorkout", { detail: wk });
      window.dispatchEvent(evt);
    });

    const del = document.createElement("button");
    del.textContent = "Delete";
    del.style.marginLeft = "0.5rem";
    del.addEventListener("click", () => {
      const evt = new CustomEvent("deleteWorkout", { detail: wk });
      window.dispatchEvent(evt);
    });

    li.appendChild(btn);
    li.appendChild(del);
    ul.appendChild(li);
  });
}

export function renderSession() {
  const container = document.getElementById("session-details");
  container.innerHTML = "";

  if (!state.session) {
    container.textContent = "No session data.";
    return;
  }

  const h3 = document.createElement("h3");
  h3.textContent = `Session for ${state.currentWorkout.name}`;
  container.appendChild(h3);

  // Let's assume session.exercises is an array
  state.session.exercises.forEach((ex) => {
    const p = document.createElement("p");
    p.textContent = `${ex.name}: ${ex.sets} sets, ${ex.reps} reps`;
    container.appendChild(p);
  });
}
