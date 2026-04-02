<table
    class="custom-table border-ridge relative box-border inline-block border-collapse rounded-sm border-8 border-primary-800 indent-0">
    <tbody>
        {{ $body }}
    </tbody>
</table>
<style>

section table td:not(.custom-td),
th,
section caption,
aside caption,
aside td {
  padding: 8px;
  background-color: #f2e6d9;
}

section tbody tr:not(.sectionTableNoPseudo, .custom-tr):after {
  content: '';
  position: absolute;
  left: 10%;
  width: 80%;
  height: 2px;
  background-color: #5f4121;
}
section table:not(#game_messages, .custom-table),
  border: 8px ridge #5f4121;
  border-radius: 5px;
  position: relative;
}

</style>
