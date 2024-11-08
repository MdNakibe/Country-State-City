@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <span>Country</span>
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createModal">Add Country</button>
                </div>
                <div class="card-body">
                    <table class="table table-dark" id="locationsTable">
                        <thead>
                            <tr>
                                <th scope="col">SL</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($countries as $key => $country)
                            <tr id="row-{{ $country->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td id="name-{{ $country->id }}">{{ $country->name }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" 
                                            onclick="openEditModal({{ $country->id }}, '{{ $country->name }}')">Edit</button>
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="deleteLocation({{ $country->id }})">Delete</button>
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
                    <h5 class="modal-title" id="createModalLabel">Add Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="mt-2">Country Name</label>
                    <input type="text" class="form-control" name="name" required>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <label class="mt-2">Country Name</label>
                    <input type="text" id="editName" class="form-control" name="name" required>
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
    // Create a country
    $('#createForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post('{{ route("countries.store") }}', formData, function (response) {
            alert(response.message);
            $('#locationsTable tbody').append(`
                <tr id="row-${response.data.id}">
                    <td>${$('#locationsTable tbody tr').length + 1}</td>
                    <td id="name-${response.data.id}">${response.data.name}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="openEditModal(${response.data.id}, '${response.data.name}')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteLocation(${response.data.id})">Delete</button>
                    </td>
                </tr>
            `);
            $('#createModal').modal('hide');
            $('#createForm')[0].reset();
        }).fail(function (error) {
            alert('Error: ' + error.responseJSON.message);
        });
    });

    // Open edit modal
    function openEditModal(id, name) {
        $('#editId').val(id);
        $('#editName').val(name);
        $('#editModal').modal('show');
    }

    // Edit a country
    $('#editForm').submit(function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        const name = $('#editName').val();

        $.ajax({
            url: '{{ route("countries.update", ":id") }}'.replace(':id', id),
            type: 'PUT',
            data: { name: name, _token: '{{ csrf_token() }}' },
            success: function (response) {
                alert(response.message);
                $(`#name-${id}`).text(name);
                $('#editModal').modal('hide');
                $('#editForm')[0].reset();
            },
            error: function (error) {
                alert('Error: ' + error.responseJSON.message);
            }
        });
    });

    // Delete a country
    function deleteLocation(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                url: '{{ route("countries.destroy", ":id") }}'.replace(':id', id),
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
