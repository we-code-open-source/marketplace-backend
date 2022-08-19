<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">

    <title>تسجيل مطعم</title>
</head>

<body class="bg-light">


    <div class="container py-5">

        <h1 class="text-center mb-5">تسجيل مطعم</h1>

        <form action="{{ url('register-restaurant') }}" method="post" class="needs-validation" novalidate>

            @csrf


            <input type="hidden" name="latitude" id="latitude" />
            <input type="hidden" name="longitude" id="longitude" />


            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            @endif


            @if( Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <p>{{ Session::get('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif




            <div class=" row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المطعم</label>
                    <input type="text" class="form-control" name="name" value="{{ request()->old('name') }}"
                        minlength="3" maxlength="100" required>
                    <div class="invalid-feedback">يجب أن يكون الاسم بين 3 إلى 100 حرف</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">عنوان المطعم</label>
                    <input type="text" class="form-control" name="address" value="{{ request()->old('address') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم تليفون المطعم</label>
                    <input type="text" class="form-control" name="phone" value="{{ request()->old('phone') }}"
                        placeholder="9XXXXXXXX" pattern="^0?[9][12345][0-9]{7}$" required maxlength="10">
                    <div class="invalid-feedback">يجب أن يكون رقم التليفون بالتنسيق (9100000000)</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم تليفون المطعم2</label>
                    <input type="text" class="form-control" name="mobile" value="{{ request()->old('mobile') }}"
                        placeholder="9XXXXXXXX" pattern="^0?[9][12345][0-9]{7}$" maxlength="10">
                    <div class="invalid-feedback">يجب أن يكون رقم التليفون بالتنسيق (9100000000)</div>
                </div>
            </div>

            <hr />

            <div class="row">
                {{-- <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" class="form-control" name="user_name" value="{{ request()->old('user_name') }}"
                        minlength="3" maxlength="32" required>
                    <div class="invalid-feedback">يجب أن يكون الاسم بين 3 إلى 100 حرف</div>
                </div> --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم تليفون المستخدم</label>
                    <input type="text" class="form-control" name="user_phone" value="{{ request()->old('user_phone') }}"
                        placeholder="9XXXXXXXX" pattern="^0?[9][12345][0-9]{7}$" required maxlength="10">
                    <div class="invalid-feedback">يجب أن يكون رقم التليفون بالتنسيق (9100000000)</div>
                </div>
                <!-- <div class="col-md-6 mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" class="form-control" name="user_email">
            </div> -->
            </div>

            <hr />

            <div class="row my-4">
                <div class="col-auto">
                    <h5>موظفين</h5>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-primary" onclick="addItemToUsersList()" type="button">أضف</button>
                    <button class="btn btn-sm btn-danger" onclick=clearUsersList() type="button">حذف الكل</button>
                </div>
            </div>

            <div id="listUsers">
                {{-- list of users --}}
            </div>

            <hr />

            <div class="row my-4">
                <div class="col-auto">
                    <h5>وجبات</h5>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-primary" onclick="addItemToFoodsList()" type="button">أضف</button>
                    <button class="btn btn-sm btn-danger" onclick=clearFoodsList() type="button">حذف الكل</button>
                </div>
            </div>

            <div id="listFoods">
                {{-- list of foods --}}
            </div>

            <hr />

            <div class="row {{ request()->has('added_by')? 'd-none' : '' }}">
                <div class="col-md-6 mb-3">
                    <label class="form-label">أضيف بواسطة</label>
                    <input type="text" class="form-control" name="added_by" value="{{ request()->get('added_by', request()->old('added_by')) }}"
                        minlength="3" maxlength="32" required>
                    <div class="invalid-feedback">يجب أن يكون الاسم بين 3 إلى 100 حرف</div>
                </div>
            </div>


            <div class="col-12 mt-3">
                <button class="btn btn-primary" style="width:100%;" type="submit">حفظ</button>
            </div>

        </form>

    </div>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()




        let latText = document.getElementById("latitude");
        let longText = document.getElementById("longitude");


        if (navigator.geolocation) {
            // Call getCurrentPosition with success and failure callbacks
            navigator.geolocation.getCurrentPosition(function (position) {
                latText.value = position.coords.latitude;
                longText.value = position.coords.longitude;
            }, function error(err) {
                alert(`ERROR(${err.code}): ${err.message}`);
            })
        }
        else {
            alert("عذرا ، متصفحك لا يدعم خدمات تحديد الموقع الجغرافي");
        }


        /* Start dynmaic list of users */

        function addItemToUsersList(phone = '', name = '') {
            id = document.getElementById('listUsers').childElementCount + 1;
            document.getElementById('listUsers').insertAdjacentHTML('beforeend', `
                <div class="row">
                    <div class="col-md mb-3">
                        <label class="form-label">رقم تليفون المستخدم</label>
                        <input type="text" data-phone class="form-control" name="users[${id}][user_phone]" value="${phone}"
                        placeholder="9XXXXXXXX" pattern="^0?[9][12345][0-9]{7}$" required maxlength="10">
                        <div class="invalid-feedback">يجب أن يكون رقم التليفون بالتنسيق (9100000000)</div>
                    </div>
                    <div class="col-md mb-3">
                        <label class="form-label">اسم المستخدم</label>
                        <input type="text" data-name class="form-control" name="users[${id}][user_name]" value="${name}"
                            minlength="3" maxlength="32">
                        <div class="invalid-feedback">يجب أن يكون الاسم بين 3 إلى 100 حرف</div>
                    </div>
                    <div class="col-md-auto mb-3 mt-md-4 pt-md-2">
                        <button class="btn btn-primary" onclick="addItemToUsersList()" type="button">أضف</button>
                        <button class="btn btn-danger" onclick=removeItemFromUsersList(this) type="button">أحذف</button>
                    </div>
                </div>
            `);
        }

        @if (request() -> old('users'))
            /* Start add old users if exists */
            @foreach(request() -> old('users') as $user)
                addItemToUsersList("{{$user['user_phone']}}", "{{$user['user_name'] ?? ''}}");
            @endforeach
            /* end add old users if exists */
        @endif

        function removeItemFromUsersList(input) {
            /* remove item from list only if there are more than one element in list */
            if (document.getElementById('listUsers').childElementCount > 1) {
                input.closest('.row').remove();
                reOrderItemsIdsInUsersList();
            }
        }

        /* Remove all items from foods list */
        function clearUsersList(){
            let list = document.getElementById('listUsers').innerHTML ='';
        }

        /* Reorder items ids to be sorted and readable for uesr when there are error in validatoins from back-end */
        function reOrderItemsIdsInUsersList() {
            let list = document.getElementById('listUsers');
            Array.from(list.children).forEach((element, index) => {
                i = index + 1;
                element.querySelector('input[data-phone]').setAttribute('name', `users[${i}][user_phone]`);
                element.querySelector('input[data-name]').setAttribute('name', `users[${i}][user_name]`);
            });
        }

        /* End dynmaic list of users */
        


        /* Start dynmaic list of foods */

        function foodsSelect(rowId,value = ''){

           return `
            <select class="form-select" data-category name="foods[${id}][category_id]" value="${value}">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>`;
        }

        function addItemToFoodsList(name = '',price = '',category='') {
            id = document.getElementById('listFoods').childElementCount + 1;
            document.getElementById('listFoods').insertAdjacentHTML('beforeend', `
                <div class="row">
                    <div class="col-md mb-3">
                        <label class="form-label">الفئة</label>
                        ${foodsSelect(id,category)}
                        <div class="invalid-feedback">يجب اختيار عنصر</div>
                    </div>
                    <div class="col-md mb-3">
                        <label class="form-label">اسم الوجبة</label>
                        <input type="text" data-name class="form-control" name="foods[${id}][name]" value="${name}" minlength="3" maxlength="32" required>
                        <div class="invalid-feedback">يجب أن يكون الاسم بين 3 إلى 32 حرف</div>
                    </div>
                    <div class="col-md mb-3">
                        <label class="form-label">السعر</label>
                        <input type="number" data-name class="form-control" name="foods[${id}][price]" value="${price}" min="0" step="any" required>
                        <div class="invalid-feedback">حقل إجباري</div>
                    </div>
                    <div class="col-md-auto mb-3 mt-md-4 pt-md-2">
                        <button class="btn btn-primary" onclick="addItemToFoodsList()" type="button">أضف</button>
                        <button class="btn btn-danger" onclick=removeItemFromFoodsList(this) type="button">أحذف</button>
                    </div>
                </div>
            `);

            if(category){ // set value of select category
                document.getElementById('listFoods').querySelector(`select[name="foods[${id}][category_id]"]`).value = category;
            }  
        }

        @if (request() -> old('foods'))
            /* Start add old foods if exists */

            @foreach(request()->old('foods') as $food)
            @php Log:: info($food) @endphp
                addItemToFoodsList("{{$food['name']}}","{{$food['price']}}", "{{$food['category_id']}}");
            @endforeach
            /* end add old foods if exists */
        @endif

        function removeItemFromFoodsList(input) {
            /* remove item from list only if there are more than one element in list */
            if (document.getElementById('listFoods').childElementCount > 1) {
                input.closest('.row').remove();
                reOrderItemsIdsInFoodsList();
            }
        }

        /* Remove all items from foods list */
        function clearFoodsList(){
            let list = document.getElementById('listFoods').innerHTML ='';
        }

        /* Reorder items ids to be sorted and readable for uesr when there are error in validatoins from back-end */
        function reOrderItemsIdsInFoodsList() {
            let list = document.getElementById('listFoods');
            Array.from(list.children).forEach((element, index) => {
                i = index + 1;
                element.querySelector('input[data-name]').setAttribute('name', `foods[${i}][name]`);
                element.querySelector('input[data-price]').setAttribute('name', `foods[${i}][price]`);
                element.querySelector('select[data-category]').setAttribute('name', `foods[${i}][category_id]`);
            });
        }

        /* End dynmaic list of foods */

    </script>
</body>

</html>