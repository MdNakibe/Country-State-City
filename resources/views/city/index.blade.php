@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <span>City</span>
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createModal">Add City</button>
                </div>
                <div class="card-body">
                    <table class="table table-dark" id="citiesTable">
                        <thead>
                            <tr>
                                <th scope="col">SL</th>
                                <th scope="col">Name</th>
                                <th scope="col">State</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cities as $key => $city)
                            <tr id="row-{{ $city->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td id="name-{{ $city->id }}">{{ $city->name }}</td>
                                <td id="state-name-{{ $city->id }}">{{ $city->state->name }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" 
                                            onclick="openEditModal({{ $city->id }}, '{{ $city->name }}', {{ $city->state_id }})">Edit</button>
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="deleteCity({{ $city->id }})">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Add City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="mt-2">City Name</label>
                    <input type="text" class="form-control" name="name" required>

                    <label class="mt-2">State</label>
                    <select class="form-control" name="state_id" required>
                        <option value="" disabled selected>Select State</option>
                        @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <label class="mt-2">City Name</label>
                    <input type="text" id="editName" class="form-control" name="name" required>

                    <label class="mt-2">State</label>
                    <select class="form-control" id="editState" name="state_id" required>
                        @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script>
    $('#createForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post('{{ route("cities.store") }}', formData, function (response) {
            alert(response.message);
            $('#citiesTable tbody').append(`
                <tr id="row-${response.data.id}">
                    <td>${$('#citiesTable tbody tr').length + 1}</td>
                    <td id="name-${response.data.id}">${response.data.name}</td>
                    <td id="state-name-${response.data.id}">${response.data.state.name}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="openEditModal(${response.data.id}, '${response.data.name}', ${response.data.state_id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCity(${response.data.id})">Delete</button>
                    </td>
                </tr>
            `);
            $('#createModal').modal('hide');
            $('#createForm')[0].reset();
        }).fail(function (error) {
            alert('Error: ' + error.responseJSON.message);
        });
    });
    function openEditModal(id, name, state_id) {
        $('#editId').val(id);
        $('#editName').val(name);
        $('#editState').val(state_id);
        $('#editModal').modal('show');
    }
    $('#editForm').submit(function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route("cities.update", ":id") }}'.replace(':id', id),
            type: 'PUT',
            data: formData,
            success: function (response) {
                alert(response.message);
                $(`#name-${id}`).text(response.data.name);
                $(`#state-name-${id}`).text(response.data.state.name);
                $('#editModal').modal('hide');
                $('#editForm')[0].reset();
            },
            error: function (error) {
                alert('Error: ' + error.responseJSON.message);
            }
        });
    });

    // Delete a city
    function deleteCity(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                url: '{{ route("cities.destroy", ":id") }}'.replace(':id', id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    alert(response.message);
                    $(`#row-${id}`).remove();
                },
                error: function (error) {
                    alert('Error: ' + error.responseJSON.message);
                }
            });
        }
    }
</script>
@endsection
