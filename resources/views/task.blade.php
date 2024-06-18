<!DOCTYPE html>
<html>
<head>
    <title>Todo App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .container h1 {
            margin: 0 0 20px;
            font-size: 24px;
            text-align: center;
        }
        .task-form {
            display: flex;
            margin-bottom: 20px;
        }
        .task-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
        }
        .task-form button {
            padding: 10px;
            border: none;
            background: #6a11cb;
            color: #fff;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        .task-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .task-list li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            background-color: #f2f2f2; 
            margin-bottom: 8px; 
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task-list li button {
            background: #ff4d4d;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: none; 
        }
        .task-list li:hover button {
            display: block; 
        }
        .task-list li button:hover {
            background: #ff1a1a;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
        .footer button {
            padding: 10px 20px;
            background-color: #6a11cb;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .task-count-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .task-count-container span {
            font-weight: bold;
        }
        .no-task-message {
            text-align: center;
            margin-top: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: left">Todo App</h1>
        <form class="task-form" id="task-form">
            <input type="text" id="title" placeholder="Add your new todo" required>
            <button type="submit"><i class="fas fa-plus"></i></button>
        </form>
        <ul class="task-list" id="task-list"></ul>
        <div class="footer">
            <div class="task-count-container" id="task-count-container">
               <span>You have  <span id="task-count">0</span> Pending Task</span>
                <button id="delete-all-btn">Delete All</button>
            </div>
            <p class="no-task-message" id="no-task-message">No tasks. Create one!</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const taskList = document.getElementById('task-list');
            const taskForm = document.getElementById('task-form');
            const taskCountContainer = document.getElementById('task-count-container');
            const taskCountSpan = document.getElementById('task-count');
            const deleteAllButton = document.getElementById('delete-all-btn');
            const noTaskMessage = document.getElementById('no-task-message');

            fetch('/api/tasks')
                .then(response => response.json())
                .then(data => {
                    data.tasks.forEach(task => {
                        addTaskToDOM(task);
                    });
                    updateTaskCount();
                    toggleNoTaskMessage();
                });

            // Add task
            taskForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const title = document.getElementById('title').value;

                fetch('/api/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ title })
                })
                .then(response => response.json())
                .then(data => {
                    addTaskToDOM(data.task);
                    taskForm.reset();
                    updateTaskCount();
                    toggleNoTaskMessage(); 
                });
            });

            // Delete task
            taskList.addEventListener('click', function (e) {
                if (e.target.tagName === 'BUTTON' || e.target.classList.contains('fa-trash')) {
                    const taskId = e.target.closest('li').dataset.id;

                    fetch(`/api/tasks/${taskId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(() => {
                        e.target.closest('li').remove();
                        updateTaskCount();
                        toggleNoTaskMessage(); 
                    });
                }
            });

            // Delete all tasks
            deleteAllButton.addEventListener('click', function () {
                fetch('/api/tasks', {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(() => {
                    taskList.innerHTML = ''; 
                    updateTaskCount();
                    toggleNoTaskMessage();
                });
            });

            function addTaskToDOM(task) {
                const li = document.createElement('li');
                li.textContent = task.title;
                li.dataset.id = task.id;

                const deleteButton = document.createElement('button');
                deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                deleteButton.classList.add('delete-btn'); 

                deleteButton.addEventListener('click', function () {
                    deleteTask(task.id);
                });

                li.appendChild(deleteButton);

                taskList.appendChild(li);
            }

            function updateTaskCount() {
                const taskCount = taskList.children.length;
                taskCountSpan.textContent = taskCount;

                if (taskCount === 0) {
                    taskCountContainer.style.display = 'none';
                } else {
                    taskCountContainer.style.display = 'flex';
                }
            }

            function toggleNoTaskMessage() {
                if (taskList.children.length === 0) {
                    noTaskMessage.style.display = 'block';
                } else {
                    noTaskMessage.style.display = 'none';
                }
            }

            function deleteTask(taskId) {
                fetch(`/api/tasks/${taskId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(() => {
                    document.querySelector(`li[data-id="${taskId}"]`).remove();
                    updateTaskCount();
                    toggleNoTaskMessage(); 
                });
            }
        });
    </script>
</body>
</html>
