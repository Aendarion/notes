window.addEventListener('load', main);

function main() {
    let domElements = {
        todoInput : document.querySelector('#todoInput'),
        newTodoDiv : document.querySelector('#newTodo'),
        mainDiv : document.querySelector('#main'),
        deleteButton : document.querySelector('.deleteSelected'),
        filtersParent : document.querySelector('.filters'),
    };
    addListenersToInput(); //add new task onblur input
    function addListenersToInput(){
        domElements.todoInput.addEventListener('blur', addNewTask);
        domElements.todoInput.addEventListener('keydown', function(event){
            if (event.keyCode === 13) { //Enter
                addNewTask();
            }
        });

        function addNewTask(){
            if (!domElements.todoInput.value ||
                domElements.todoInput.value.trim().length === 0) return false;
            let newTask = createNewTaskDiv(domElements.todoInput.value);
            domElements.newTodoDiv.after(newTask);
            domElements.todoInput.value = '';
        }

        function createNewTaskDiv(text) {
            let newDiv = document.createElement('div');
            newDiv.classList = 'todo';
            newDiv.append(createCheckbox());
            newDiv.append(createP(text));
            return newDiv;

            function createP(text) {
                let newP = document.createElement('p');
                newP.type = 'checkbox';
                newP.textContent = text;
                newP.classList = 'taskText';
                return newP;
            }

            function createCheckbox() {
                let newCheckbox = document.createElement('input');
                newCheckbox.type = 'checkbox';
                newCheckbox.classList = 'status';
                return newCheckbox;
            }
        }
    }

    domElements.mainDiv.addEventListener('click', catchCheckboxClick);
    function catchCheckboxClick(event) {
        let target = event.target;
        while (target.classList != 'status') {
            if (target === document) {
                return false;
            }
            target = target.parentNode;
        }
        changeButtonVisibility();
    }

    function changeButtonVisibility() {
        if (isAnySelected()) {
            domElements.deleteButton.style.visibility = 'visible';
        } else {
            domElements.deleteButton.style.visibility = 'hidden';
        }

        function isAnySelected(){
            let checkboxes = document.querySelectorAll('.status');
            for (let i=0; i<checkboxes.length; i++){
                if (checkboxes[i].checked === true) return true;
            }
            return false;
        }

    }

    domElements.deleteButton.addEventListener('click', removeSelected);
    function removeSelected() {
        let checkboxes = document.querySelectorAll('.status');
        for (let i=0; i<checkboxes.length; i++){
            if (checkboxes[i].checked === true) {
                checkboxes[i].parentNode.remove();
            }
        }
        domElements.deleteButton.style.visibility = 'hidden';
    }

    domElements.filtersParent.addEventListener('click', filterTasks);

    function filterTasks(event){
        let availableFilters = {
            'All' : function () {
                this.showAll();
            },
            'Active' : function () {
                let checkboxes = document.querySelectorAll('.status');
                this.showAll();
                for (let i=0; i<checkboxes.length; i++){
                    if (checkboxes[i].checked === true) {
                        checkboxes[i].parentNode.style.display = 'none';
                    }
                }
            },
            'Completed' : function () {
                let checkboxes = document.querySelectorAll('.status');
                this.showAll();
                for (let i=0; i<checkboxes.length; i++){
                    if (checkboxes[i].checked !== true) {
                        checkboxes[i].parentNode.style.display = 'none';
                    }
                }
            },
            showAll : function () {
                let checkboxes = document.querySelectorAll('.status');
                for (let i=0; i<checkboxes.length; i++){
                    checkboxes[i].parentNode.style.display = 'block';
                }
            }
        }


        availableFilters[determineFilter(event)]();

        function determineFilter(event){
            let target = event.target;
            while (target.tagName != 'LI') {
                if (target === document) {
                    return false;
                }
                target = target.parentNode;
            }
            domElements.filtersParent.childNodes.forEach(key => {
                key.classList = '';
            });
            target.classList = 'selected';
            return target.textContent;
        }
    }

    document.querySelector('.save').addEventListener('click', saveToMysql);
    async function saveToMysql(){
        let mainContent = document.querySelector('#main').innerHTML;
        let response = await fetch('http://localhost/first/php/saveHtmlText.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'text/plain;charset=UTF-8'
            },
            body: mainContent,
        });
        let result = await response.text();
        alert(result);
    }

}