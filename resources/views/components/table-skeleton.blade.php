@props([
    'cols' => 5,
    'rows' => 4,
])

<tbody class="divide-y divide-slate-100">
    @for($r = 0; $r < $rows; $r++)
        <tr class="animate-pulse">
            @for($c = 0; $c < $cols; $c++)
                <td class="px-4 py-3.5 whitespace-nowrap">
                    @if($c === 0)
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-200/70 shrink-0"></div>
                            <div class="space-y-1.5 min-w-0 flex-1">
                                <div class="h-3.5 bg-slate-200/80 rounded-md w-3/4"></div>
                                <div class="h-2.5 bg-slate-200/50 rounded-md w-1/2"></div>
                            </div>
                        </div>
                    @elseif($c === $cols - 1)
                        <div class="flex items-center justify-end gap-1.5">
                            <div class="w-7 h-7 rounded-lg bg-slate-200/60"></div>
                            <div class="w-7 h-7 rounded-lg bg-slate-200/60"></div>
                        </div>
                    @else
                        <div class="h-3.5 bg-slate-200/60 rounded-md w-2/3"></div>
                    @endif
                </td>
            @endfor
        </tr>
    @endfor
</tbody>
